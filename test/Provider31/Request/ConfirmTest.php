<?php

/**
 *      Class for Confirm request
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPayTest\Provider31;

use EasyPayTest\TestCase;
use EasyPay\Provider31\Request\Confirm;
use EasyPay\Provider31\Request\RAW;

class ConfirmTest extends TestCase
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

    public function test_PaymentId()
    {
        file_put_contents('php://input', <<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Confirm>
    <ServiceId>1234</ServiceId>
    <PaymentId>5169801</PaymentId>
  </Confirm>
</Request>
EOD
        );
        $r = new Confirm(new RAW());

        $this->assertEquals(
            $r->PaymentId(),
            '5169801'
        );
    }

        /**
         * @expectedException EasyPay\Exception\Structure
         * @expectedExceptionCode -57
         * @expectedExceptionMessage There is no PaymentId element in the xml request!
         */
        public function test_validate_request_noPaymentId()
        {
            $options = array(
                    'ServiceId' => 1234
            );
            file_put_contents('php://input', <<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Confirm>
    <ServiceId>1234</ServiceId>
  </Confirm>
</Request>
EOD
            );
            $r = new Confirm(new RAW());
            $r->validate_request($options);
        }

        /**
         * @expectedException EasyPay\Exception\Structure
         * @expectedExceptionCode -56
         * @expectedExceptionMessage There is more than one PaymentId element in the xml-query!
         */
        public function test_validate_request_twoPaymentId()
        {
            $options = array(
                    'ServiceId' => 1234
            );
            file_put_contents('php://input', <<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Confirm>
    <ServiceId>1234</ServiceId>
    <PaymentId>5169801</PaymentId>
    <PaymentId>6056011</PaymentId>
  </Confirm>
</Request>
EOD
            );
            $r = new Confirm(new RAW());
            $r->validate_request($options);
        }

        public function test_validate_request()
        {
            $options = array(
                    'ServiceId' => 1234
            );
            file_put_contents('php://input', <<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Confirm>
    <ServiceId>1234</ServiceId>
    <PaymentId>6056011</PaymentId>
  </Confirm>
</Request>
EOD
            );
            $r = new Confirm(new RAW());
            $r->validate_request($options);
        }
}
