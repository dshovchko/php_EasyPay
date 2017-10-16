<?php

/**
 *      General class for all request types
 *
 *      @package php_EasyPay
 *      @version 1.0
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPay\Provider31\Request;

use EasyPay\Log as Log;

class General
{
        /**
         *      @var string 'DateTime' node
         */
        protected $DateTime;
        
        /**
         *      @var string 'Sign' node
         */
        protected $Sign;
        
        /**
         *      @var string 'Operation' type
         */
        protected $Operation;
        
        /**
         *      @var string 'ServiceId' node
         */
        protected $ServiceId;
        
        /**
         *      @var array list of possible operations
         */
        protected $operations = array('Check','Payment','Confirm','Cancel');
        
        /**
         *      General constructor
         *      
         *      @param string $raw Raw request data
         */
        public function __construct($raw)
        {
                $this->raw_request = $raw;
                
                $this->parse_request_data();
        }
        
        /**
         *      Get DateTime
         *
         *      @return string
         */
        public function DateTime()
        {
                return $this->DateTime;
        }
        
        /**
         *      Get Sign
         *
         *      @return string
         */
        public function Sign()
        {
                return $this->Sign;
        }
        
        /**
         *      Get Operation type
         *
         *      @return string
         */
        public function Operation()
        {
                return $this->Operation;
        }
        
        /**
         *      Get ServiceId
         *
         *      @return string
         */
        public function ServiceId()
        {
                return $this->ServiceId;
        }
        
        /**
         *      Parse xml-request, which was previously "extracted" from the body of the http request
         *
         *      @throws Exception
         */
        protected function parse_request_data()
        {
                if ($this->raw_request == NULL)
                {
                        Log::instance()->error('The xml request from the HTTP request body was not received');
                        throw new \Exception('Error in request', -50);
                }
                if (strlen($this->raw_request) == 0)
                {
                        Log::instance()->error('An empty xml request');
                        throw new \Exception('Error in request', -51);
                }
                
                $doc = new \DOMDocument();
                $doc->loadXML($this->raw_request);
                
                // process <Request> group
                $r = $this->getNodes($doc, 'Request');
                
                if (count($r) > 1)
                {
                        Log::instance()->error('There is more than one Request element in the xml-query!');
                        throw new \Exception('Error in request', -52);
                }
                elseif (count($r) < 1)
                {
                        Log::instance()->error('The xml-query does not contain any element Request!');
                        throw new \Exception('Error in request', -52);
                }
                
                foreach ($r[0]->childNodes as $child)
                {
                        if ($child->nodeName == 'DateTime')
                        {
                                $this->parse_request_node($child, 'DateTime');
                        }
                        elseif ($child->nodeName == 'Sign')
                        {
                                $this->parse_request_node($child, 'Sign');
                        }
                        elseif (in_array($child->nodeName, $this->operations))
                        {
                                if ( ! isset($this->Operation))
                                {
                                        $this->Operation = $child->nodeName;
                                }
                                else
                                {
                                        Log::instance()->error('There is more than one Operation type element in the xml-query!');
                                        throw new \Exception('Error in request', -53);
                                }
                        }
                }
                
                if ( ! isset($this->Operation))
                {
                        Log::instance()->error('There is no Operation type element in the xml request!');
                        throw new \Exception('Error in request', -55);
                }
                
                // process <Operation> group
                $r = $this->getNodes($doc, $this->Operation);
                
                foreach ($r[0]->childNodes as $child)
                {
                        if ($child->nodeName == 'ServiceId')
                        {
                                $this->parse_request_node($child, 'ServiceId');
                        }
                }
        }
        
        /**
         *      Parse node of request
         *
         *      @param DOMNode $n
         *      @param string $name
         *
         *      @throws Exception
         */
        protected function parse_request_node($n, $name)
        {
                if ( ! isset($this->$name))
                {
                        $this->$name = $n->nodeValue;
                }
                else
                {
                        Log::instance()->error('There is more than one '.$name.' element in the xml-query!');
                        throw new \Exception('Error in request', -56);
                }
        }
        
        /**
         *      "Rough" validation of the received xml request 
         *
         *      @param array $options
         *      @throws Exception
         */
        public function validate_request($options)
        {
                if ( ! isset($this->DateTime))
                {
                        Log::instance()->error('There is no DateTime element in the xml request!');
                        throw new \Exception('Error in request', -57);
                }
                if ( ! isset($this->Sign))
                {
                        Log::instance()->error('There is no Sign element in the xml request!');
                        throw new \Exception('Error in request', -57);
                }
                if ( ! isset($this->ServiceId))
                {
                        Log::instance()->error('There is no ServiceId element in the xml request!');
                        throw new \Exception('Error in request', -57);
                }
                
                // compare received value ServiceId with option ServiceId
                if (intval($options['ServiceId']) != intval($this->ServiceId))
                {
                        Log::instance()->error('This request is not for our ServiceId!');
                        throw new \Exception('This request is not for us', -58);
                }
        }
        
        /**
         *      Verify signature of request
         *
         *      @param array $options
         *      @throws Exception
         */
        public function verify_sign($options)
        {
                if ( ! file_exists($options['EasySoftPKey']))
                {
                        Log::instance()->error('The file with the public key EasyPay was not find!');
                        throw new \Exception('Error while processing request', -98);
                }
                
                // this code is written according to the easysoft example

                $fpkey = fopen($options['EasySoftPKey'], "rb");
                if ($fpkey === FALSE)
                {
                        Log::instance()->error('The file with the public key EasyPay was not open!');
                        throw new \Exception('Error while processing request', -97);
                }
                $pkeyid = fread($fpkey, 8192);
                if ($pkeyid === FALSE)
                {
                        Log::instance()->error('The file with the public key EasyPay was not read!');
                        throw new \Exception('Error while processing request', -97);
                }
                fclose($fpkey);
                
                $pub_key = openssl_pkey_get_public($pkeyid);
                if ($pub_key === FALSE)
                {
                        Log::instance()->error('Can not extract the public key from certificate!');
                        throw new \Exception('Error while processing request', -97);
                }
                $bin_sign = pack("H*", $this->Sign);
                $xml = str_replace($this->Sign, '', $this->raw_request);
                $check = openssl_verify($xml, $bin_sign, $pub_key);
                if ($check == -1)
                {
                        Log::instance()->error('Error verify signature of request!');
                        throw new \Exception('Error while processing request', -96);
                }
                elseif ($check == 0)
                {
                        Log::instance()->error('Signature of request is incorrect!');
                        throw new \Exception('Error while processing request', -95);
                }
        }
        
        /**
         *      Selects nodes by name
         *
         *      @param DOMDocument $dom
         *      @param string $name
         *      @param array $ret
         *
         *      @return array nodes with the name 
         */
        protected function getNodes($dom, $name, $ret=array())
        {
                foreach($dom->childNodes as $child)
                {
                        if ($child->nodeName == $name)
                        {
                                array_push($ret, $child);
                        }   
                        else
                        {
                                if (count($child->childNodes) > 0)
                                {
                                        $ret = $this->getNodes($child, $name, $ret);
                                }
                        }
                }
                
                return $ret;
        }
        
}