<?php

/**
 *      Class for Payment request
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPayTest\Provider31;

use EasyPayTest\TestCase;
use EasyPay\Provider31\Request\Payment;
use EasyPay\Provider31\Request\RAW;

class PaymentTest extends TestCase
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

    public function test_Account()
    {
        file_put_contents('php://input', <<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Payment>
    <ServiceId>127</ServiceId>
    <OrderId>17212</OrderId>
    <Account>64229400128</Account>
    <Amount>25</Amount>
  </Payment>
</Request>
EOD
        );
        $r = new Payment(new RAW());

        $this->assertEquals(
            $r->Account(),
            '64229400128'
        );
    }

        public function test_OrderId()
        {
            file_put_contents('php://input', <<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Payment>
    <ServiceId>127</ServiceId>
    <OrderId>17212</OrderId>
    <Account>64229400128</Account>
    <Amount>25</Amount>
  </Payment>
</Request>
EOD
            );
            $r = new Payment(new RAW());

            $this->assertEquals(
                $r->OrderId(),
                '17212'
            );
        }

        public function test_Amount()
        {
            file_put_contents('php://input', <<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Payment>
    <ServiceId>127</ServiceId>
    <OrderId>17212</OrderId>
    <Account>64229400128</Account>
    <Amount>25</Amount>
  </Payment>
</Request>
EOD
            );
            $r = new Payment(new RAW());

            $this->assertEquals(
                $r->Amount(),
                '25'
            );
        }

        /**
         * @expectedException EasyPay\Exception\Structure
         * @expectedExceptionCode -57
         * @expectedExceptionMessage There is no Account element in the xml request!
         */
        public function test_validate_request_noAccount()
        {
            $options = array(
                    'ServiceId' => 127
            );
            file_put_contents('php://input', <<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Payment>
    <ServiceId>127</ServiceId>
    <OrderId>17212</OrderId>
    <Amount>25</Amount>
  </Payment>
</Request>
EOD
            );
            $r = new Payment(new RAW());
            $r->validate_request($options);
        }

        /**
         * @expectedException EasyPay\Exception\Structure
         * @expectedExceptionCode -56
         * @expectedExceptionMessage There is more than one Account element in the xml-query!
         */
        public function test_validate_request_twoAccount()
        {
            $options = array(
                    'ServiceId' => 127
            );
            file_put_contents('php://input', <<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Payment>
    <ServiceId>127</ServiceId>
    <OrderId>17212</OrderId>
    <Account>64229400128</Account>
    <Amount>25</Amount>
    <Account>29400728</Account>
  </Payment>
</Request>
EOD
            );
            $r = new Payment(new RAW());
            $r->validate_request($options);
        }

        /**
         * @expectedException EasyPay\Exception\Structure
         * @expectedExceptionCode -57
         * @expectedExceptionMessage There is no OrderId element in the xml request!
         */
        public function test_validate_request_noOrderId()
        {
            $options = array(
                    'ServiceId' => 127
            );
            file_put_contents('php://input', <<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Payment>
    <ServiceId>127</ServiceId>
    <Account>64229400128</Account>
    <Amount>25</Amount>
  </Payment>
</Request>
EOD
            );
            $r = new Payment(new RAW());
            $r->validate_request($options);
        }

        /**
         * @expectedException EasyPay\Exception\Structure
         * @expectedExceptionCode -56
         * @expectedExceptionMessage There is more than one OrderId element in the xml-query!
         */
        public function test_validate_request_twoOrderId()
        {
            $options = array(
                    'ServiceId' => 127
            );
            file_put_contents('php://input', <<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Payment>
    <ServiceId>127</ServiceId>
    <OrderId>17212</OrderId>
    <Account>64229400128</Account>
    <Amount>25</Amount>
    <OrderId>19321</OrderId>
  </Payment>
</Request>
EOD
            );
            $r = new Payment(new RAW());
            $r->validate_request($options);
        }

        /**
         * @expectedException EasyPay\Exception\Structure
         * @expectedExceptionCode -57
         * @expectedExceptionMessage There is no Amount element in the xml request!
         */
        public function test_validate_request_noAmount()
        {
            $options = array(
                    'ServiceId' => 127
            );
            file_put_contents('php://input', <<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Payment>
    <ServiceId>127</ServiceId>
    <OrderId>17212</OrderId>
    <Account>64229400128</Account>
  </Payment>
</Request>
EOD
            );
            $r = new Payment(new RAW());
            $r->validate_request($options);
        }

        /**
         * @expectedException EasyPay\Exception\Structure
         * @expectedExceptionCode -56
         * @expectedExceptionMessage There is more than one Amount element in the xml-query!
         */
        public function test_validate_request_twoAmount()
        {
            $options = array(
                    'ServiceId' => 127
            );
            file_put_contents('php://input', <<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Payment>
    <ServiceId>127</ServiceId>
    <OrderId>17212</OrderId>
    <Account>64229400128</Account>
    <Amount>25</Amount>
    <Amount>32</Amount>
  </Payment>
</Request>
EOD
            );
            $r = new Payment(new RAW());
            $r->validate_request($options);
        }

        public function test_validate_request()
        {
            $options = array(
                    'ServiceId' => 127
            );
            file_put_contents('php://input', <<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Payment>
    <ServiceId>127</ServiceId>
    <OrderId>17212</OrderId>
    <Account>64229400128</Account>
    <Amount>25</Amount>
  </Payment>
</Request>
EOD
            );
            $r = new Payment(new RAW());
            $r->validate_request($options);
        }
}
