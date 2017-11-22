<?php

/**
 *      Main class for EasyPay-Provider 3.1
 *
 *      @package php_EasyPay
 *      @version 1.1
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
     *      @var \EasyPay\Callback
     */
    protected static $cb;

    /**
     *      @var mixed
     */
    private $request;

    /**
     *      @var Provider31\Response
     */
    private $response;

    /**
     *      Provider31 constructor
     *
     *      @param array $options
     *      @param Callback $cb
     *      @param \Debulog\LoggerInterface $log
     */
    public function __construct(array $options, \EasyPay\Callback $cb, \Debulog\LoggerInterface $log)
    {
        self::$options = array_merge(self::$options, $options);
        self::$cb = $cb;

        Log::set($log);
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
        catch (Exception\Structure $e)
        {
            $this->response = $this->get_error_response($e->getCode(), 'Error in request');
        }
        catch (Exception\Sign $e)
        {
            $this->response = $this->get_error_response($e->getCode(), 'Signature error!');
        }
        catch (Exception\Runtime $e)
        {
            $this->response = $this->get_error_response($e->getCode(), 'Error while processing request');
        }
        catch (\Exception $e)
        {
            $this->response = $this->get_error_response($e->getCode(), $e->getMessage());
        }

        //      output response
        $this->response->out(self::$options);
    }

    /**
     *      Process request and generate response
     *
     *      @throws Exception\Structure
     */
    private function get_response()
    {
        switch ($this->request->Operation())
        {
            case 'Check':

                return $this->response_check();

            case 'Payment':

                return $this->response_payment();

            case 'Confirm':

                return $this->response_confirm();

            case 'Cancel';

                return $this->response_cancel();

            default:
                break;
        }

        throw new Exception\Structure('There is not supported value of Operation in xml-request!', -99);
    }

    /**
     *      run check callback and generate a response
     *
     *      @return Provider31\Response\Check
     */
    private function response_check()
    {
        Log::instance()->add(sprintf('Check("%s")', $this->request->Account()));

        $accountinfo = self::$cb->check(
            $this->request->Account()
        );

        // Sending a response
        return new Provider31\Response\Check($accountinfo);
    }

    /**
     *      run payment callback and generate a response
     *
     *      @return Provider31\Response\Payment
     */
    private function response_payment()
    {
        Log::instance()->add(sprintf('Payment("%s", "%s", "%s")', $this->request->Account(), $this->request->OrderId(), $this->request->Amount()));

        $paymentid = self::$cb->payment(
            $this->request->Account(),
            $this->request->OrderId(),
            $this->request->Amount()
        );

        // Sending a response
        return new Provider31\Response\Payment($paymentid);
    }

    /**
     *      run confirm callback and generate a response
     *
     *      @return Provider31\Response\Confirm
     */
    private function response_confirm()
    {
        Log::instance()->add(sprintf('Confirm("%s")', $this->request->PaymentId()));

        $orderdate = self::$cb->confirm(
            $this->request->PaymentId()
        );

        // Sending a response
        return new Provider31\Response\Confirm($orderdate);
    }

    /**
     *      run cancel callback and generate a response
     *
     *      @return Provider31\Response\Cancel
     */
    private function response_cancel()
    {
        Log::instance()->add(sprintf('Cancel("%s")', $this->request->PaymentId()));

        $canceldate = self::$cb->cancel(
            $this->request->PaymentId()
        );

        // Sending a response
        return new Provider31\Response\Cancel($canceldate);
    }

    /**
     *      Generates an xml with an error message
     *
     *      @param integer $code
     *      @param string $message
     *
     *      @return Provider31\Response\ErrorInfo
     */
    private function get_error_response($code, $message)
    {
        Log::instance()->add('the request was processed with an error');

        // Sending a response
        return new Provider31\Response\ErrorInfo($code, $message);
    }
}
