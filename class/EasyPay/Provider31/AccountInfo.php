<?php

namespace EasyPay\Provider31;

class AccountInfo implements \Iterator
{
        private $_accountinfo;
        
        public function __construct($accountinfo) {
                $this->_accountinfo = $accountinfo;
        }
        
        public function rewind() {
                reset($this->_accountinfo);
        }
        
        public function current() {
                return current($this->_accountinfo);
        }
        
        public function key() {
                return key($this->_accountinfo);
        }
        
        public function next() {
                next($this->_accountinfo);
        }
        
        public function valid() {
                return key($this->_accountinfo) !== null;
        }
}