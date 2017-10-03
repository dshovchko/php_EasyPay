<?php

function __autoload_EasyPay($class_name) 
{
        if (substr($class_name, 0, 8) != 'EasyPay\\')
        {
                return FALSE;
        }
        
        $filename = str_replace('\\', DIRECTORY_SEPARATOR, substr($class_name, 8));
        
        $file = dirname(__FILE__).'/'.$filename.'.php';

        if ( ! file_exists($file))
        {
                return FALSE;
        }
        require $file;
}

spl_autoload_extensions('.php');
spl_autoload_register('__autoload_EasyPay');