<?php

/**
 *      Class singleton for logging
 *
 *      @package php_EasyPay
 *      @version 1.0
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPay;

class Log {
    
        /**
         *      @var string
         */
        public static $DIR;
    
        /**
         *      @var string
         */
        public static $PREFIX;
    
        /**
         *      @var boolean
         */
        public static $DEBUG = FALSE;
        
        /**
         *      @var string
         */
        public static $EMAILFROM;
        
        /**
         *      @var string
         */
        public static $EMAILTO;
        
        /**
         *      @var Log
         */
        protected static $_instance;
        
        /**
         *      @var array
         */
        protected $_messages = array();
        
        /**
         *      @var array
         */
        protected $_errors = array();
        
        /**
         *      @var array
         */
        protected $_debugs = array();
    
        /**
         *      gets the instance via lazy initialization (created on first usage)
         *
         *      @return Log
         */
        public static function instance()
        {
                if (self::$_instance === NULL)
                {
                        // Create a new instance
                        self::$_instance = new Log;
                        
                        // Write the logs at shutdown
                        register_shutdown_function(array(self::$_instance, 'write'));
                }
                
                return self::$_instance;
        }

        /**
         *      is not allowed to call from outside to prevent from creating multiple instances,
         *      to use the singleton, you have to obtain the instance from Log::instance() instead
         */
        private function __construct()
        {
                if (self::$DEBUG === TRUE)
                {
                        $this->_debugs[] = PHP_EOL . 'start debugging at ' . strftime('%d.%m.%Y %H:%M:%S ') . PHP_EOL;
                }
        }
        /**
         *      prevent the instance from being cloned (which would create a second instance of it)
         */
        private function __clone()
        {
        }
        
        /**
         *      prevent from being unserialized (which would create a second instance of it)
         */
        private function __wakeup()
        {
        }

        /**
         *      Add message to logger buffer
         *
         *      @param string $message Message text
         *
         */
        public function add($message)
        {
                $this->_messages[] = strftime('%d.%m.%Y %H:%M:%S ') . $message . PHP_EOL;
                $this->debug($message);
        }

        /**
         *      Add error message to logger buffer
         *
         *      @param string $message Message text
         *
         */
        public function error($message)
        {
                $this->_errors[] = strftime('%d.%m.%Y %H:%M:%S ') . $message . PHP_EOL;
                $this->debug('ERROR: '.$message);
        }

        /**
         *      Add debug message to logger buffer
         *
         *      @param string $message Message to log
         *
         */
        public function debug($message)
        {
                if (self::$DEBUG === TRUE)
                {
                        $this->_debugs[] = $message . PHP_EOL;
                }
        }

        /**
         *      Sync all buffers to files
         *
         */
        public function write()
        {
                if (self::$DEBUG === TRUE)
                {
                        $this->_debugs[] = 'end of debugging at ' . strftime('%d.%m.%Y %H:%M:%S ') . PHP_EOL . PHP_EOL;
                }
                
                if (!empty($this->_messages))
                {
                        $this->_write($this->_messages, self::$DIR . self::$PREFIX . '.log');
                        $this->_messages = array();
                }
                
                if (!empty($this->_debugs))
                {
                        $this->_write($this->_debugs, self::$DIR . self::$PREFIX . '_debug.log');
                        $this->debugs = array();
                }
                
                if (!empty($this->_errors))
                {
                        $this->_write($this->_errors, self::$DIR . self::$PREFIX . '_error.log');
                        
                        $msg = implode('', $this->_errors);
                        
                        $this->errors = array();
                        
                        if (isset(self::$EMAILFROM) && isset(self::$EMAILTO))
                        {
                                $this->_mail($msg, self::$EMAILFROM, self::$EMAILTO);
                        }
                }
        }

        /**
         *      Write text to file
         *
         *      @param string $messages
         *      @param string $file
         *
         *      @throws Exception
         *
         */
        protected function _write($messages, $file)
        {
                $f = @fopen($file, 'a');
                if ($f === false)
                {
                        throw new Exception("Logfile $file is not writeable!");
                }
                
                foreach($messages as $msg)
                {
                        fwrite($f, $msg);
                }
                
                fclose($f);
        }
        
        /**
         *      Send message via email
         *
         *      @param string $msg
         *      @param string $from
         *      @param string $to
         *
         */
        protected function _mail($msg, $from, $to)
        {
                $headers = 'From: ' . $from . PHP_EOL;
                $headers .= 'X-Mailer: PHP/' . PHP_VERSION . PHP_EOL;
                $headers .= 'Content-type: text/plain; charset=utf-8' . PHP_EOL;
                
                $msg .= PHP_EOL . PHP_EOL . 'Payment Gateway EasyPay';
                
                mail($to, '[EasyPay] Error message', $msg, $headers);
        }
}
