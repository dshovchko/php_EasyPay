<?php

/**
 *      Class for Check request
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPay\Provider31\Request;

use EasyPay\Log as Log;
use EasyPay\Exception;

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
         *      @throws Exception\Structure
         */
        public function validate_request($options)
        {
                parent::validate_request($options);
                
                if ( ! isset($this->Account))
                {
                        throw new Exception\Structure('There is no Account element in the xml request!', -57);
                }
        }
}
