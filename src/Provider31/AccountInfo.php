<?php

/**
 *      Class iterator for a set of parameters with account information
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPay\Provider31;

class AccountInfo implements \Iterator
{
        /**
         *      @var array $_accountinfo {
         *              @var string $parameter Name of parameter
         *              @var string $value     Value of parameter
         *      }
         *
         */
        private $_accountinfo;
        
        /**
         *      AccountInfo constructor
         *
         *      @param array $accountinfo
         *
         */
        public function __construct($accountinfo) {
                $this->_accountinfo = $accountinfo;
        }
        
        public function rewind() {
                reset($this->_accountinfo);
        }
        
        /**
         *      @return mixed $_accountinfo {
         *              @var string $parameter Name of parameter
         *              @var string $value     Value of parameter
         *      }
         *
         */
        public function current() {
                return current($this->_accountinfo);
        }
        
        /**
         *      @return mixed
         *
         */
        public function key() {
                return key($this->_accountinfo);
        }
        
        /**
         *      @return mixed $_accountinfo {
         *              @var string $parameter Name of parameter
         *              @var string $value     Value of parameter
         *      }
         *
         */
        public function next() {
                next($this->_accountinfo);
        }
        
        /**
         *      @return boolean
         */
        public function valid() {
                return key($this->_accountinfo) !== null;
        }
}