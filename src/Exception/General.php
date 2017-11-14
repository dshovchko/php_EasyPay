<?php

/**
 *      General Exception class
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPay\Exception;

use EasyPay\Log as Log;

class General extends \RuntimeException implements EInterface
{
        /**
         *      Constructor
         *
         *      @param string $message
         *      @param \Throwable $previous
         */
        public function __construct($message, $code=0, \Exception $previous=null, $tracelog=false)
        {
                Log::instance()->error($message.(($tracelog)?PHP_EOL.$this->getTraceAsString():null));
                parent::__construct($message, $code, $previous);
        }
}
