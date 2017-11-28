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
use EasyPay\Provider31\Request\RAW;

class CheckTest extends TestCase
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
  <Check>
    <ServiceId>1234</ServiceId>
    <Account>987654321</Account>
  </Check>
</Request>
EOD
        );
        $r = new Check(new RAW());

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
            file_put_contents('php://input', <<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Check>
    <ServiceId>1234</ServiceId>
  </Check>
</Request>
EOD
            );
            $r = new Check(new RAW());
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
            file_put_contents('php://input', <<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Check>
    <Account>109876543</Account>
    <ServiceId>1234</ServiceId>
    <Account>987654321</Account>
  </Check>
</Request>
EOD
            );
            $r = new Check(new RAW());
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
  <Check>
    <Account>109876543</Account>
    <ServiceId>1234</ServiceId>
  </Check>
</Request>
EOD
            );
            $r = new Check(new RAW());
            $r->validate_request($options);
        }
}
