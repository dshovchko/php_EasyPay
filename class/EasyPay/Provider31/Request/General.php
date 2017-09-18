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
                $this->validate_request();
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
         *      Parse xml-request, which was previously "extracted" from the body of the http request
         *
         *      @throws Exception
         */
        protected function parse_request_data()
        {
                if ($this->raw_request == NULL)
                {
                        Log::instance()->error('The xml request from the HTTP request body was not received');
                        throw new \Exception('Error in request', -99);
                }
                if (strlen($this->raw_request) == 0)
                {
                        Log::instance()->error('An empty xml request');
                        throw new \Exception('Error in request', -99);
                }
                
                $doc = new \DOMDocument();
                $doc->loadXML($this->raw_request);
                $r = $this->getNodes($doc, 'Request');
                
                if (count($r) != 1)
                {
                        Log::instance()->error('There is more than one Request element in the xml-query!');
                        throw new \Exception('Error in request', -99);
                }
                
                foreach ($r[0]->childNodes as $child)
                {
                        if ($child->nodeName == 'DateTime')
                        {
                                if ( ! isset($this->DateTime))
                                {
                                        $this->DateTime = $child->nodeValue;
                                }
                                else
                                {
                                        Log::instance()->error('There is more than one DateTime element in the xml-query!');
                                        throw new \Exception('Error in request', -99);
                                }
                        }
                        elseif ($child->nodeName == 'Sign')
                        {
                                if ( ! isset($this->Sign))
                                {
                                        $this->Sign = $child->nodeValue;
                                }
                                else
                                {
                                        Log::instance()->error('There is more than one Sign element in the xml-query!');
                                        throw new \Exception('Error in request', -99);
                                }
                        }
                        elseif (in_array($child->nodeName, $this->operations))
                        {
                                if ( ! isset($this->Operation))
                                {
                                        $this->Operation = $child->nodeName;
                                }
                        }
                }
        }
        
        /**
         *      "Rough" validation of the received xml request 
         *
         *      @throws Exception
         */
        protected function validate_request()
        {
                if ( ! isset($this->DateTime))
                {
                        Log::instance()->error('There is no DateTime element in the xml request!');
                        throw new \Exception('Error in request', -99);
                }
                if ( ! isset($this->Sign))
                {
                        Log::instance()->error('There is no Sign element in the xml request!');
                        throw new \Exception('Error in request', -99);
                }
                if ( ! isset($this->Operation))
                {
                        Log::instance()->error('There is no Operation type element in the xml request!');
                        throw new \Exception('Error in request', -99);
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