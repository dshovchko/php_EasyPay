<?php

/**
 *      Class for Cancel request
 *
 *      @package php_EasyPay
 *      @version 1.0
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPayTest\Provider31;

use EasyPayTest\TestCase;
use EasyPay\Provider31\Request\Cancel;

class CancelTest extends TestCase
{
        public function test_PaymentId()
        {
                $raw =<<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Cancel>
    <ServiceId>1234</ServiceId>
    <PaymentId>732329</PaymentId>
  </Cancel>
</Request>
EOD;
                $r = new Cancel($raw);
                
                $this->assertEquals(
                    $r->PaymentId(),
                    '732329'
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
  <Cancel>
    <ServiceId>1234</ServiceId>
  </Cancel>
</Request>
EOD;
                $r = new Cancel($raw);
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
  <Cancel>
    <ServiceId>1234</ServiceId>
    <PaymentId>732329</PaymentId>
    <PaymentId>493327</PaymentId>
  </Cancel>
</Request>
EOD;
                $r = new Cancel($raw);
                $r->validate_request($options);
        }
}