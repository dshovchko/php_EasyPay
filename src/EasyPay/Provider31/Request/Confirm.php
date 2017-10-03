<?php

/**
 *      Class for Confirm request
 *
 *      @package php_EasyPay
 *      @version 1.0
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPay\Provider31\Request;

use EasyPay\Log as Log;

class Confirm extends General
{
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
                $r = $this->getNodes($doc, 'Confirm');
                
                foreach ($r[0]->childNodes as $child)
                {
                        if ($child->nodeName == 'PaymentId')
                        {
                                $this->parse_request_node($child, 'PaymentId');
                        }
                }
        }
        
        /**
         *      validate Confirm request
         *
         *      @param array $options
         *      @throws Exception
         */
        public function validate_request($options)
        {
                parent::validate_request($options);
                
                if ( ! isset($this->PaymentId))
                {
                        Log::instance()->error('There is no PaymentId element in the xml request!');
                        throw new \Exception('Error in request', -99);
                }
        }
}
