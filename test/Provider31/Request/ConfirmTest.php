<?php

/**
 *      Class for Confirm request
 *
 *      @package php_EasyPay
 *      @version 1.0
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPayTest\Provider31;

use EasyPayTest\TestCase;
use EasyPay\Provider31\Request\Confirm;

class ConfirmTest extends TestCase
{
        public function test_PaymentId()
        {
                $raw =<<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Confirm>
    <ServiceId>1234</ServiceId>
    <PaymentId>5169801</PaymentId>
  </Confirm>
</Request>
EOD;
                $r = new Confirm($raw);
                
                $this->assertEquals(
                    $r->PaymentId(),
                    '5169801'
                );
        }
        
        /**
         * @expectedException Exception
         * @expectedExceptionCode -57
         * @expectedExceptionMessage Error in request
         */
        public function test_validate_request_noPaymentId()
        {
                $options = array(
                        'ServiceId' => 1234
                );
                $raw =<<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Confirm>
    <ServiceId>1234</ServiceId>
  </Confirm>
</Request>
EOD;
                $r = new Confirm($raw);
                $r->validate_request($options);
        }
        
        /**
         * @expectedException Exception
         * @expectedExceptionCode -56
         * @expectedExceptionMessage Error in request
         */
        public function test_validate_request_twoPaymentId()
        {
                $options = array(
                        'ServiceId' => 1234
                );
                $raw =<<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Confirm>
    <ServiceId>1234</ServiceId>
    <PaymentId>5169801</PaymentId>
    <PaymentId>6056011</PaymentId>
  </Confirm>
</Request>
EOD;
                $r = new Confirm($raw);
                $r->validate_request($options);
        }
}