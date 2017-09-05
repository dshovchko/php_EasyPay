<?php

class EasyPay_Provider31
{
        protected static $log;
        
        public function __construct($log_instance)
        {
                self::$log = $log_instance;
        }
        
        public function process()
        {
                try
                {
                        // retrieve data from a POST request
                        $this->get_http_post_raw_data();
                        
                        // parse the data request
                        $this->parse_request_data();
                        
                        $this->raw_response = $this->get_response()->friendly();
                        
                        self::$log->add('the request was processed successfully');
                }
                catch (Exception $e)
                {
                        $this->raw_response = $this->get_error_response($e->getCode(), $e->getMessage())->friendly();
                        
                        self::$log->add('the request was processed with an error');
                }
                
                $this->format_response();
                
                self::$log->debug('response sends: ');
                self::$log->debug($this->formated_response);
                
                ob_clean();
                header("Content-Type: text/xml; charset=utf-8");
                echo $this->formated_response;
                exit;
        }

}