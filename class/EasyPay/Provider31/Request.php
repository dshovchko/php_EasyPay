<?php

namespace EasyPay\Provider31;

use EasyPay\Log as Log;

final class Request
{
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
                                
                        case 'Confirm':
                                
                        case 'Cancel';
                                
                        default:
                                Log::instance()->error('There is not supported value of Operation in xml-request!');
                                throw new \Exception('Error in request', 99);
                                break;
                }
        }
        
        /**
         *   Get data from the body of the http request
         *
         *   - with the appropriate configuration of php.ini they can be found
         *     in the global variable $HTTP_RAW_POST_DATA
         *
         *   - but it's easier just to read the data from the php://input stream,
         *     which does not depend on the php.ini directives and allows you to read
         *     raw data from the request body
         */
        private static function get_http_raw_post_data()
        {
                $raw_request = file_get_contents('php://input');
                
                Log::instance()->debug('request received: ');
                Log::instance()->debug($raw_request);
                Log::instance()->debug(' ');
                
                return $raw_request;
        }
}