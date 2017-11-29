<?php

namespace EasyPayTest;

use \EasyPayTest\TestCase;
use \EasyPay\Provider31 as Provider31;
use \EasyPay\Provider31\AccountInfo as AccountInfo;

class StubProvider31 extends Provider31
{
    protected $e;
    public function __construct($e)
    {
        $this->e = $e;
    }
    public function get_request()
    {
        throw $this->e;
    }
}

class TestCallback implements \EasyPay\Callback
{
    public function check($account)
    {
        return new AccountInfo(array(
                'Account' => '1234567890',
        ));
    }

    public function payment($account, $orderid, $amount)
    {
        return '1234567890';
    }

    public function confirm($paymentid)
    {
        return '1234567890';
    }

    public function cancel($paymentid)
    {
        return '1234567890';
    }
}

class Provider31Test extends TestCase
{
    public function SetUp()
    {
        $existed = in_array('php', stream_get_wrappers());
        if ($existed) {
            stream_wrapper_unregister('php');
        }
        stream_wrapper_register('php', '\EasyPayTest\MockPHPStream');

        $_SERVER['REMOTE_ADDR'] = '1.2.3.4';

        parent::SetUp();
    }

    public function TearDown()
    {
        stream_wrapper_restore('php');

        parent::TearDown();
    }

    public function XMLcheck()
    {
        return <<<EOD
<Request>
    <DateTime>2017-10-01T11:11:11</DateTime>
    <Sign></Sign>
    <Check>
        <ServiceId>1234</ServiceId>
        <Account>987654321</Account>
    </Check>
</Request>
EOD;
    }

    public function XMLpayment()
    {
        return <<<EOD
<Request>
    <DateTime>2017-10-16T15:12:32</DateTime>
    <Sign></Sign>
    <Payment>
        <ServiceId>1234</ServiceId>
        <OrderId>43232128933</OrderId>
        <Account>1256789032</Account>
        <Amount>10</Amount>
    </Payment>
</Request>
EOD;
    }

    public function XMLconfirm()
    {
        return <<<EOD
<Request>
    <DateTime>2017-10-16T15:14:14</DateTime>
    <Sign></Sign>
    <Confirm>
        <ServiceId>1234</ServiceId>
        <PaymentId>534320043</PaymentId>
    </Confirm>
</Request>
EOD;
    }

    public function XMLcancel()
    {
        return <<<EOD
<Request>
    <DateTime>2017-10-16T15:14:44</DateTime>
    <Sign></Sign>
    <Cancel>
        <ServiceId>1234</ServiceId>
        <PaymentId>534320043</PaymentId>
    </Cancel>
</Request>
EOD;
    }

    public function XMLnoOperation()
    {
        return <<<EOD
<Request>
    <DateTime>2017-10-16T15:14:44</DateTime>
    <Sign></Sign>
</Request>
EOD;
    }

    public function test_constructor_default_options()
    {
        $options = array();
        $cb = new TestCallback();
        $log = new StubLogger('');

        $p = new Provider31($options, $cb, $log);

        $this->assertEquals(
            array(
                'ServiceId' => 0,
                'UseSign' => false,
                'EasySoftPKey' => '',
                'ProviderPKey' => '',
            ),
            $this->invokeProperty($p, 'options')->getValue($p)
        );
    }

    public function test_constructor_options()
    {
        $options = array(
            'ServiceId' => 1234,
            'www' => 'eee',
            'UseSign' => false,
            'EasySoftPKey' => '/qqq',
            'ProviderPKey' => '/www/eee',
        );
        $cb = new TestCallback();
        $log = new StubLogger('');

        $p = new Provider31($options, $cb, $log);

        $this->assertEquals(
            array(
                'ServiceId' => 1234,
                'www' => 'eee',
                'UseSign' => false,
                'EasySoftPKey' => '/qqq',
                'ProviderPKey' => '/www/eee',
            ),
            $this->invokeProperty($p, 'options')->getValue($p)
        );
    }

    public function test_get_request_check()
    {
        $options = array();
        $cb = new TestCallback();
        $log = new StubLogger('');

        $p = new Provider31($options, $cb, $log);

        file_put_contents('php://input', $this->XMLcheck());

        $this->invokeMethod($p, 'get_request', array(null));
        $this->assertInstanceOf(
            \EasyPay\Provider31\Request\Check::class,
            $this->invokeProperty($p, 'request')->getValue($p)
        );
    }

    public function test_get_request_payment()
    {
        $options = array();
        $cb = new TestCallback();
        $log = new StubLogger('');

        $p = new Provider31($options, $cb, $log);

        file_put_contents('php://input', $this->XMLpayment());

        $this->invokeMethod($p, 'get_request', array(null));
        $this->assertInstanceOf(
            \EasyPay\Provider31\Request\Payment::class,
            $this->invokeProperty($p, 'request')->getValue($p)
        );
    }

    public function test_get_request_confirm()
    {
        $options = array();
        $cb = new TestCallback();
        $log = new StubLogger('');

        $p = new Provider31($options, $cb, $log);

        file_put_contents('php://input', $this->XMLconfirm());

        $this->invokeMethod($p, 'get_request', array(null));
        $this->assertInstanceOf(
            \EasyPay\Provider31\Request\Confirm::class,
            $this->invokeProperty($p, 'request')->getValue($p)
        );
    }

    public function test_get_request_cancel()
    {
        $options = array();
        $cb = new TestCallback();
        $log = new StubLogger('');

        $p = new Provider31($options, $cb, $log);

        file_put_contents('php://input', $this->XMLcancel());

        $this->invokeMethod($p, 'get_request', array(null));
        $this->assertInstanceOf(
            \EasyPay\Provider31\Request\Cancel::class,
            $this->invokeProperty($p, 'request')->getValue($p)
        );
    }

    /**
     * @expectedException EasyPay\Exception\Structure
     * @expectedExceptionCode -55
     * @expectedExceptionMessage There is no Operation type element in the xml request!
     */
    public function test_get_request_exception()
    {
        $options = array();
        $cb = new TestCallback();
        $log = new StubLogger('');

        $p = new Provider31($options, $cb, $log);

        file_put_contents('php://input', $this->XMLnoOperation());
        $this->invokeMethod($p, 'get_request', array(null));
    }

    public function test_get_response_check()
    {
        $options = array();
        $cb = new TestCallback();
        $log = new StubLogger('');

        $p = new Provider31($options, $cb, $log);

        file_put_contents('php://input', $this->XMLcheck());
        $this->invokeMethod($p, 'get_request', array(null));

        $this->assertInstanceOf(
            \EasyPay\Provider31\Response\Check::class,
            $this->invokeMethod($p, 'get_response', array(null))
        );
    }

    public function test_get_response_payment()
    {
        $options = array();
        $cb = new TestCallback();
        $log = new StubLogger('');

        $p = new Provider31($options, $cb, $log);

        file_put_contents('php://input', $this->XMLpayment());
        $this->invokeMethod($p, 'get_request', array(null));

        $this->assertInstanceOf(
            \EasyPay\Provider31\Response\Payment::class,
            $this->invokeMethod($p, 'get_response', array(null))
        );
    }

    public function test_get_response_confirm()
    {
        $options = array();
        $cb = new TestCallback();
        $log = new StubLogger('');

        $p = new Provider31($options, $cb, $log);

        file_put_contents('php://input', $this->XMLconfirm());
        $this->invokeMethod($p, 'get_request', array(null));

        $this->assertInstanceOf(
            \EasyPay\Provider31\Response\Confirm::class,
            $this->invokeMethod($p, 'get_response', array(null))
        );
    }

    public function test_get_response_cancel()
    {
        $options = array();
        $cb = new TestCallback();
        $log = new StubLogger('');

        $p = new Provider31($options, $cb, $log);

        file_put_contents('php://input', $this->XMLcancel());
        $this->invokeMethod($p, 'get_request', array(null));

        $this->assertInstanceOf(
            \EasyPay\Provider31\Response\Cancel::class,
            $this->invokeMethod($p, 'get_response', array(null))
        );
    }

    /**
     * @expectedException EasyPay\Exception\Structure
     * @expectedExceptionCode -55
     * @expectedExceptionMessage There is no Operation type element in the xml request!
     */
    public function test_get_response_exception()
    {
        $options = array();
        $cb = new TestCallback();
        $log = new StubLogger('');

        $p = new Provider31($options, $cb, $log);

        file_put_contents('php://input', $this->XMLnoOperation());
        $this->invokeProperty($p, 'request')->setValue(
            $p,
            $this->invokeMethod($p, 'get_request', array(null))
        );
    }

    public function test_get_error_response()
    {
        $options = array();
        $cb = new TestCallback();
        $log = new StubLogger('');

        $p = new Provider31($options, $cb, $log);

        $this->assertInstanceOf(
            \EasyPay\Provider31\Response\ErrorInfo::class,
            $this->invokeMethod($p, 'get_error_response', array('1234', 'Error response message'))
        );
    }

    public function test_process_check()
    {
        $options = array();
        $cb = new TestCallback();
        $log = new StubLogger('');

        $p = new Provider31($options, $cb, $log);

        file_put_contents('php://input', $this->XMLcheck());
        $dt = date('Y-m-d\TH:i:s', time());
        $this->expectOutputString(<<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<Response>
  <StatusCode>0</StatusCode>
  <StatusDetail>checked</StatusDetail>
  <DateTime>$dt</DateTime>
  <AccountInfo>
    <Account>1234567890</Account>
  </AccountInfo>
</Response>

EOD
        );
        $p->process();
    }

    public function test_process_cancel()
    {
        $options = array();
        $cb = new TestCallback();
        $log = new StubLogger('');

        $p = new Provider31($options, $cb, $log);

        file_put_contents('php://input', $this->XMLcancel());
        $dt = date('Y-m-d\TH:i:s', time());
        $this->expectOutputString(<<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<Response>
  <StatusCode>0</StatusCode>
  <StatusDetail>checked</StatusDetail>
  <DateTime>$dt</DateTime>
  <CancelDate>1234567890</CancelDate>
</Response>

EOD
        );
        $p->process();
    }

    public function test_process_confirm()
    {
        $options = array();
        $cb = new TestCallback();
        $log = new StubLogger('');

        $p = new Provider31($options, $cb, $log);

        file_put_contents('php://input', $this->XMLconfirm());
        $dt = date('Y-m-d\TH:i:s', time());
        $this->expectOutputString(<<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<Response>
  <StatusCode>0</StatusCode>
  <StatusDetail>checked</StatusDetail>
  <DateTime>$dt</DateTime>
  <OrderDate>1234567890</OrderDate>
</Response>

EOD
        );
        $p->process();
    }

    public function test_process_payment()
    {
        $options = array();
        $cb = new TestCallback();
        $log = new StubLogger('');

        $p = new Provider31($options, $cb, $log);

        file_put_contents('php://input', $this->XMLpayment());
        $dt = date('Y-m-d\TH:i:s', time());
        $this->expectOutputString(<<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<Response>
  <StatusCode>0</StatusCode>
  <StatusDetail>checked</StatusDetail>
  <DateTime>$dt</DateTime>
  <PaymentId>1234567890</PaymentId>
</Response>

EOD
        );
        $p->process();
    }

    public function test_process_exception_structure()
    {
        $p = new StubProvider31(new \EasyPay\Exception\Structure('aaa', -54));
        $dt = date('Y-m-d\TH:i:s', time());
        $this->expectOutputString(<<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<Response>
  <StatusCode>-54</StatusCode>
  <StatusDetail>Error in request</StatusDetail>
  <DateTime>$dt</DateTime>
</Response>

EOD
        );
        $p->process();
    }

    public function test_process_exception_sign()
    {
        $p = new StubProvider31(new \EasyPay\Exception\Sign('aaa', -30));
        $dt = date('Y-m-d\TH:i:s', time());
        $this->expectOutputString(<<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<Response>
  <StatusCode>-30</StatusCode>
  <StatusDetail>Signature error!</StatusDetail>
  <DateTime>$dt</DateTime>
</Response>

EOD
        );
        $p->process();
    }

    public function test_process_exception_runtime()
    {
        $p = new StubProvider31(new \EasyPay\Exception\Runtime('aaa', -62));
        $dt = date('Y-m-d\TH:i:s', time());
        $this->expectOutputString(<<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<Response>
  <StatusCode>-62</StatusCode>
  <StatusDetail>Error while processing request</StatusDetail>
  <DateTime>$dt</DateTime>
</Response>

EOD
        );
        $p->process();
    }

    public function test_process_exception()
    {
        $p = new StubProvider31(new \Exception('aaa', -99));
        $dt = date('Y-m-d\TH:i:s', time());
        $this->expectOutputString(<<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<Response>
  <StatusCode>-99</StatusCode>
  <StatusDetail>aaa</StatusDetail>
  <DateTime>$dt</DateTime>
</Response>

EOD
        );
        $p->process();
    }
}
