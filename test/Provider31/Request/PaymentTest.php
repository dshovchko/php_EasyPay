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

class PaymentTest extends TestCase
{
        public function test_Account()
        {
                $raw =<<<EOD
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
EOD;
                $r = new Payment($raw);
                
                $this->assertEquals(
                    $r->Account(),
                    '64229400128'
                );
        }
        
        public function test_OrderId()
        {
                $raw =<<<EOD
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
EOD;
                $r = new Payment($raw);
                
                $this->assertEquals(
                    $r->OrderId(),
                    '17212'
                );
        }
        
        public function test_Amount()
        {
                $raw =<<<EOD
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
EOD;
                $r = new Payment($raw);
                
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
                $raw =<<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Payment>
    <ServiceId>127</ServiceId>
    <OrderId>17212</OrderId>
    <Amount>25</Amount>
  </Payment>
</Request>
EOD;
                $r = new Payment($raw);
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
                $raw =<<<EOD
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
EOD;
                $r = new Payment($raw);
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
                $raw =<<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Payment>
    <ServiceId>127</ServiceId>
    <Account>64229400128</Account>
    <Amount>25</Amount>
  </Payment>
</Request>
EOD;
                $r = new Payment($raw);
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
                $raw =<<<EOD
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
EOD;
                $r = new Payment($raw);
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
                $raw =<<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Payment>
    <ServiceId>127</ServiceId>
    <OrderId>17212</OrderId>
    <Account>64229400128</Account>
  </Payment>
</Request>
EOD;
                $r = new Payment($raw);
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
                $raw =<<<EOD
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
EOD;
                $r = new Payment($raw);
                $r->validate_request($options);
        }
}