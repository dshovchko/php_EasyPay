<?php

/**
 *      Exception class for runtime occurs
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */
namespace EasyPay\Exception;

use EasyPay\Log as Log;

class Runtime extends General implements EInterface
{
        /**
         *      Constructor
         *
         *      @param string $message
         *      @param Throwable $previous
         */
        public function __construct($message, $code=0, \Exception $previous=null)
        {
                parent::__construct($message, $code, $previous, true);
        }
}
