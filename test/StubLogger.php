<?php

/**
 *      Class for logging
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPayTest;

use Debulog;

class StubLogger extends Debulog\Logger
{
        /**
         *      Add message to logger buffer
         *
         *      @param string $message Message text
         *
         */
        public function add($message){}

        /**
         *      Add error message to logger buffer
         *
         *      @param string $message Message text
         *
         */
        public function error($message){}

        /**
         *      Add debug message to logger buffer
         *
         *      @param string $message Message to log
         *
         */
        public function debug($message){}

        /**
         *      Sync all buffers to files
         *
         */
        public function sync(){}
}