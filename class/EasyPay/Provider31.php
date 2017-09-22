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
                'UseSign' => false,
                'EasySoftPKey' => '',
                'ProviderPKey' => '',
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
                        //      get request
                        $this->request = Provider31\Request::get();
                        
                        //      validate request
                        $this->request->validate_request(self::$options);
                        Log::instance()->debug('request is valid');
                        
                        //      verify sign
                        $this->request->verify_sign(self::$options);
                        Log::instance()->debug('signature of request is correct');
                        
                        //      get response
                        $this->response = $this->get_response();
                        
                        Log::instance()->add('the request was processed successfully');
                }
                catch (\Exception $e)
                {
                        //      get error response
                        $this->raw_response = $this->get_error_response($e->getCode(), $e->getMessage())->friendly();
                        
                        Log::instance()->add('the request was processed with an error');
                }
                //      output response
                $this->response->out(self::$options);
                exit;
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
                                
                                return $this->response_confirm();
                                break;
                        
                        case 'Cancel';
                                
                                return $this->response_cancel();
                                break;
                                
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
         *      run confirm callback and generate a response
         *
         *      @return string
         */
        private function response_confirm()
        {
                $orderdate = self::$cb->confirm(
                        $this->request->PaymentId()
                );
                
                // Sending a response
                $xml = new Provider31\Response\Confirm($orderdate);
                
                return $xml;
        }
        
        /**
         *      run cancel callback and generate a response
         *
         *      @return string
         */
        private function response_cancel()
        {
                $canceldate = self::$cb->cancel(
                        $this->request->PaymentId()
                );
                
                // Sending a response
                $xml = new Provider31\Response\Cancel($canceldate);
                
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