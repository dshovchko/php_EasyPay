<?php

namespace EasyPay;

class Log {
    
    public static $DIR;
    public static $PREFIX;
    public static $DEBUG = FALSE;
    public static $EMAIL;
    
    protected static $_instance;
    
    protected $_messages = array();
    protected $_errors = array();
    protected $_debugs = array();
    
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
    
    public function __construct()
    {
        if (self::$DEBUG === TRUE)
        {
            $this->_debugs[] = 'начало отладки в ' . strftime('%d.%m.%Y %H:%M:%S ') . PHP_EOL;
        }
    }
    
    public function add($message)
    {
        $this->_messages[] = strftime('%d.%m.%Y %H:%M:%S ') . $message . PHP_EOL;
    }
    
    public function error($message)
    {
        $this->_errors[] = strftime('%d.%m.%Y %H:%M:%S ') . $message . PHP_EOL;
    }
    
    public function debug($message)
    {
        $this->_debugs[] = $message . PHP_EOL;
    }
    
    public function write()
    {
        if (self::$DEBUG === TRUE)
        {
            $this->_debugs[] = 'конец отладки в ' . strftime('%d.%m.%Y %H:%M:%S ') . PHP_EOL . PHP_EOL;
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
            
            $this->_mail($msg);
        }
    }
    
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
    
    protected function _mail($msg)
    {
	$headers = "From: privatbank@x.doris.ua\n";
	$headers .= "X-Mailer: PHP/" . phpversion() . "\n";
	$headers .= 'Content-type: text/plain; charset=cp1251'."\r\n";
        
	$msg .= "\n\nПлатежный шлюз x.doris.ua";
        
	mail(self::$EMAIL, '[x.doris.ua] Сообщение об ошибке', $msg, $headers);
    }
}
