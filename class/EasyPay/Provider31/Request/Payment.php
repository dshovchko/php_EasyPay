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
                $r = $this->getNodes($doc, 'Payment');
                
                foreach ($r[0]->childNodes as $child)
                {
                        if ($child->nodeName == 'Account')
                        {
                                $this->parse_request_node($child, 'Account');
                        }
                        if ($child->nodeName == 'OrderId')
                        {
                                $this->parse_request_node($child, 'OrderId');
                        }
                        if ($child->nodeName == 'Amount')
                        {
                                $this->parse_request_node($child, 'Amount');
                        }
                }
        }
        
        /**
         *      validate Payment request
         *
         *      @param array $options
         *      @throws Exception
         */
        public function validate_request($options)
        {
                parent::validate_request($options);
                
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
