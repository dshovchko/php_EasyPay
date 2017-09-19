<?php

/**
 *      Class for Cancel request
 *
 *      @package php_EasyPay
 *      @version 1.0
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPay\Provider31\Request;

use EasyPay\Log as Log;

class Cancel extends General
{
        /**
         *      @var string 'ServiceId' node
         */
        protected $ServiceId;
        
        /**
         *      @var string 'PaymentId' node
         */
        protected $PaymentId;
        
        /**
         *      Check constructor
         *      
         *      @param string $raw Raw request data
         */
        public function __construct($raw)
        {
                parent::__construct($raw);
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
         *      Get PaymentId
         *
         *      @return string
         */
        public function PaymentId()
        {
                return $this->PaymentId;
        }
        
        /**
         *      Parse xml-request, which was previously "extracted" from the body of the http request
         *
         *      @throws Exception
         */
        protected function parse_request_data()
        {
                parent::parse_request_data();
                
                $doc = new \DOMDocument();
                $doc->loadXML($this->raw_request);
                $r = $this->getNodes($doc, 'Request');
                
                foreach ($r[0]->childNodes as $child)
                {
                        if ($child->nodeName == 'Cancel')
                        {
                                $data = $child;
                     
                                foreach ($data->childNodes as $child2)
                                {
                                        if ($child2->nodeName == 'ServiceId')
                                        {
                                                if ( ! isset($this->ServiceId))
                                                {
                                                        $this->ServiceId = $child2->nodeValue;
                                                }
                                                else
                                                {
                                                        Log::instance()->error('There is more than one ServiceId element in the xml-query!');
                                                        throw new \Exception('Error in request', -99);
                                                }
                                        }
                                        elseif ($child2->nodeName == 'PaymentId')
                                        {
                                                if ( ! isset($this->PaymentId))
                                                {
                                                        $this->PaymentId = $child2->nodeValue;
                                                }
                                                else
                                                {
                                                        Log::instance()->error('There is more than one PaymentId element in the xml-query!');
                                                        throw new \Exception('Error in request', -99);
                                                }
                                        }
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
                parent::validate_request();
                
                if ( ! isset($this->ServiceId))
                {
                        Log::instance()->error('There is no ServiceId element in the xml request!');
                        throw new \Exception('Error in request', -99);
                }
                if ( ! isset($this->PaymentId))
                {
                        Log::instance()->error('There is no PaymentId element in the xml request!');
                        throw new \Exception('Error in request', -99);
                }
        }
}
