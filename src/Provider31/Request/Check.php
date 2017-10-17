<?php

/**
 *      Class for Check request
 *
 *      @package php_EasyPay
 *      @version 1.0
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPay\Provider31\Request;

use EasyPay\Log as Log;

class Check extends General
{
        /**
         *      @var string 'Account' node
         */
        protected $Account;
        
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
         *      Get Account
         *
         *      @return string
         */
        public function Account()
        {
                return $this->Account;
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
                $r = $this->getNodes($doc, 'Check');
                
                foreach ($r[0]->childNodes as $child)
                {
                        if ($child->nodeName == 'Account')
                        {
                                $this->parse_request_node($child, 'Account');
                        }
                }
        }
        
        /**
         *      validate Check request
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
                        throw new \Exception('Error in request', -57);
                }
        }
}
