<?php

/**
 *      Class Static Factory  to build a specific class of request
 *
 *      @package php_EasyPay
 *      @version 1.0
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPay\Provider31;

use EasyPay\Log as Log;

final class Request
{
        /**
         *      static method to create a specific class of request
         *
         *      @return General Request class of the appropriate type
         *
         */
        public static function get()
        {
                $raw = self::get_http_raw_post_data();
                
                $r = new Request\General($raw);
                
                switch ($r->Operation())
                {
                        case 'Check':
                                
                                return new Request\Check($raw);
                                break;
                                
                        case 'Payment':
                                
                                return new Request\Payment($raw);
                                break;
                                
                        case 'Confirm':
                                
                                return new Request\Confirm($raw);
                                break;
                                
                        case 'Cancel';
                                
                                return new Request\Cancel($raw);
                                break;
                                
                        default:
                                Log::instance()->error('There is not supported value of Operation in xml-request!');
                                throw new \Exception('Error in request', 99);
                                break;
                }
        }
        
        /**
         *      Get data from the body of the http request
         *
         *      - with the appropriate configuration of php.ini they can be found
         *        in the global variable $HTTP_RAW_POST_DATA
         *
         *      - but it's easier just to read the data from the php://input stream,
         *        which does not depend on the php.ini directives and allows you to read
         *        raw data from the request body
         *
         *      @return string Http raw post data
         *      
         */
        private static function get_http_raw_post_data()
        {
                Log::instance()->add('request from ' . $_SERVER['REMOTE_ADDR']);
                
                $raw_request = file_get_contents('php://input');
                
                Log::instance()->debug('request received: ');
                Log::instance()->debug($raw_request);
                Log::instance()->debug(' ');
                
                return $raw_request;
        }
}