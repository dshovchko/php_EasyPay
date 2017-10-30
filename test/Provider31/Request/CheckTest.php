<?php

/**
 *      Class for Check request
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPayTest\Provider31;

use EasyPayTest\TestCase;
use EasyPay\Provider31\Request\Check;

class CheckTest extends TestCase
{
        public function test_Account()
        {
                $raw =<<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Check>
    <ServiceId>1234</ServiceId>
    <Account>987654321</Account>
  </Check>
</Request>
EOD;
                $r = new Check($raw);
                
                $this->assertEquals(
                    $r->Account(),
                    '987654321'
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
                        'ServiceId' => 1234
                );
                $raw =<<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Check>
    <ServiceId>1234</ServiceId>
  </Check>
</Request>
EOD;
                $r = new Check($raw);
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
                        'ServiceId' => 1234
                );
                $raw =<<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Check>
    <Account>109876543</Account>
    <ServiceId>1234</ServiceId>
    <Account>987654321</Account>
  </Check>
</Request>
EOD;
                $r = new Check($raw);
                $r->validate_request($options);
        }
}