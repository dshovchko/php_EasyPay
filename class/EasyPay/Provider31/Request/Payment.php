<?php

/**
 *      Class for Payment request
 *
 *      @package php_EasyPay
 *      @version 1.0
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPay\Provider31\Request;

use EasyPay\Log as Log;

class Payment extends General
{
        /**
         *      @var string 'ServiceId' node
         */
        protected $ServiceId;
        
        /**
         *      @var string 'Account' node
         */
        protected $Account;
        
        /**
         *      @var string 'OrderId' node
         */
        protected $OrderId;
        
        /**
         *      @var string 'Amount' node
         */
        protected $Amount;
        
        /**
         *      Payment constructor
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
         *      Get Account
         *
         *      @return string
         */
        public function Account()
        {
                return $this->Account;
        }
        
        /**
         *      Get OrderId
         *
         *      @return string
         */
        public function OrderId()
        {
                return $this->OrderId;
        }
        
        /**
         *      Get Amount
         *
         *      @return string
         */
        public function Amount()
        {
                return $this->Amount;
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
                        if ($child->nodeName == 'Payment')
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
                                        elseif ($child2->nodeName == 'Account')
                                        {
                                                if ( ! isset($this->Account))
                                                {
                                                        $this->Account = $child2->nodeValue;
                                                }
                                                else
                                                {
                                                        Log::instance()->error('There is more than one Account element in the xml-query!');
                                                        throw new \Exception('Error in request', -99);
                                                }
                                        }
                                        elseif ($child2->nodeName == 'OrderId')
                                        {
                                                if ( ! isset($this->OrderId))
                                                {
                                                        $this->OrderId = $child2->nodeValue;
                                                }
                                                else
                                                {
                                                        Log::instance()->error('There is more than one OrderId element in the xml-query!');
                                                        throw new \Exception('Error in request', -99);
                                                }
                                        }
                                        elseif ($child2->nodeName == 'Amount')
                                        {
                                                if ( ! isset($this->Amount))
                                                {
                                                        $this->Amount = $child2->nodeValue;
                                                }
                                                else
                                                {
                                                        Log::instance()->error('There is more than one Amount element in the xml-query!');
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
                if ( ! isset($this->Account))
                {
                        Log::instance()->error('There is no Account element in the xml request!');
                        throw new \Exception('Error in request', -99);
                }
                if ( ! isset($this->OrderId))
                {
                        Log::instance()->error('There is no OrderId element in the xml request!');
                        throw new \Exception('Error in request', -99);
                }
                if ( ! isset($this->Amount))
                {
                        Log::instance()->error('There is no Amount element in the xml request!');
                        throw new \Exception('Error in request', -99);
                }
        }
}
