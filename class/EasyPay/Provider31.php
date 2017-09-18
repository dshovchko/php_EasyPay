<?php

/**
 *      Main class for EasyPay-Provider 3.1
 *
 *      @package php_EasyPay
 *      @version 1.0
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */


namespace EasyPay;

class Provider31
{
        /**
         *      @var array 
         */
        protected static $options = array(
                'ServiceId' => 0,
        );
        /**
         *      @var Callback
         */
        protected static $cb;
        
        /**
         *      @var Request
         */
        private $request;
        
        /**
         *      Provider31 constructor
         *
         *      @param array $options
         *      @param Callback $cb
         *
         */
        public function __construct(array $options, Callback $cb)
        {
                self::$options = array_merge(self::$options, $options);
                self::$cb = $cb;
        }
        
        /**
         *      Get and process request, echo response
         *
         */
        public function process()
        {
                try
                {
                        $this->request = Provider31\Request::get();
                        $this->validate_request();
                        
                        $this->raw_response = $this->get_response()->friendly();
                        
                        Log::instance()->add('the request was processed successfully');
                }
                catch (\Exception $e)
                {
                        $this->raw_response = $this->get_error_response($e->getCode(), $e->getMessage())->friendly();
                        
                        Log::instance()->add('the request was processed with an error');
                }
                
                $this->format_response();
                
                Log::instance()->debug('response sends: ');
                Log::instance()->debug($this->formated_response);
                
                ob_clean();
                header("Content-Type: text/xml; charset=utf-8");
                echo $this->formated_response;
                exit;
        }
        
        /**
         *      Format a nice xml output
         *
         */
        private function format_response()
        {
                $xml = new \DomDocument();
                $xml->formatOutput = true;
                $xml->preserveWhitespace = false;
                $xml->loadXML($this->raw_response);
                
                $this->formated_response = $xml->saveXML();
        }
        
        /**
         *      validation of the received request
         *
         */
        private function validate_request()
        {
                // compare received value ServiceId with option ServiceId
                if (intval(self::$options['ServiceId']) != intval($this->request->ServiceId()))
                {
                        Log::instance()->error('This request is not for our ServiceId!');
                        throw new \Exception('This request is not for us', -98);
                }
        }
        
        /**
         *      Process request and generate response
         *
         */
        private function get_response()
        {
                switch ($this->request->Operation())
                {
                        case 'Check':
                                
                                return $this->response_check();
                                break;
                                
                        case 'Payment':
                                
                                return $this->response_payment();
                                break;
                                
                        case 'Confirm':
                                
                        case 'Cancel';
                                
                        default:
                                break;
                }
                
                Log::instance()->error('There is not supported value of Operation in xml-request!');
                throw new \Exception('Error in request', 99);
        }
        
        /**
         *      run check callback and generate a response
         *
         *      @return string
         */
        private function response_check()
        {
                $accountinfo = self::$cb->check($this->request->Account());
                
                // Sending a response
                $xml = new Provider31\Response\Check($accountinfo);
                
                return $xml;
        }
        
        /**
         *      run payment callback and generate a response
         *
         *      @return string
         */
        private function response_payment()
        {
                $paymentid = self::$cb->payment(
                        $this->request->Account(),
                        $this->request->OrderId(),
                        $this->request->Amount()
                );
                
                // Sending a response
                $xml = new Provider31\Response\Payment($paymentid);
                
                return $xml;
        }

        
        /**
         *      Generates an xml with an error message
         *
         *      @param integer $code
         *      @param string $message
         *
         *      @return string
         */
        private function get_error_response($code, $message)
        {
                // Sending a response
                $errxml = new Provider31\Response\ErrorInfo($code, $message);
                
                return $errxml;
        }
}