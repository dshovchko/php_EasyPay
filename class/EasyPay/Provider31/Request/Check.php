<?php

namespace EasyPay\Provider31\Request;

use EasyPay\Log as Log;

class Check extends General
{
        protected $ServiceId;
        protected $Account;
        
        public function __construct($raw)
        {
                parent::__construct($raw);
        }
        
        public function ServiceId()
        {
                return $this->ServiceId;
        }
        
        public function Account()
        {
                return $this->Account;
        }
        
        /**
         *   Parse xml-request, which was previously "extracted" from the body of the http request
         */
        protected function parse_request_data()
        {
                parent::parse_request_data();
                
                $doc = new \DOMDocument();
                $doc->loadXML($this->raw_request);
                $r = $this->getNodes($doc, 'Request');
                
                foreach ($r[0]->childNodes as $child)
                {
                        if ($child->nodeName == 'Check')
                        {
                                $data = $child;
                                $this->request['Check'] = array();
                     
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
                                }
                        }
                }
        }
        
        /**
         *   validation of the received xml request Check
         *   check the Check node and the child nodes ServiceId and Account
         *
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
        }
}
