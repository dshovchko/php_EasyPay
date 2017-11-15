<?php

/**
 *      Singleton class for logging
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPay;

use Debulog;

class Log {

        /**
         *      @var Debulog\LoggerInterface
         */
        protected static $_instance;

        /**
         *      gets the instance
         *
         *      @return Debulog\LoggerInterface
         */
        public static function instance()
        {
                return self::$_instance;
        }

        /**
         *      sets the logger instance
         *
         *      @param Debulog\LoggerInterface $log
         */
        public static function set(Debulog\LoggerInterface $log)
        {
                self::$_instance = $log;
        }
}
