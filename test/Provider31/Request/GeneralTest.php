<?php

/**
 *      General class for all request types
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPayTest\Provider31;

use EasyPayTest\TestCase;
use EasyPay\Provider31\Request\General;
use EasyPay\Provider31\Request\RAW;

class GeneralTest extends TestCase
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
        <ServiceId>4321</ServiceId>
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
        <ServiceId>4321</ServiceId>
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
        <ServiceId>4321</ServiceId>
        <PaymentId>534320043</PaymentId>
    </Cancel>
</Request>
EOD;
    }

    public function XMLsign()
    {
        return <<<EOD
<Request>
    <DateTime>2017-10-16T14:06:07</DateTime>
    <Sign>5F9ABA6AB64621F4DEDC42E8B90201A0D6203A725B54919719A2E05D323729CC5CD96D03115C31310FA90B38B953394537336A9B38C2DC2AB2367D85D04E1A19478B867A1BC03231FB78A7EA3C9FE74C7EE033CB3AB42159D639FE627953E8EF15C299F4A0902C96EF00F40677819FE5CF7A16B38544E70668024DA96F4AEE5E</Sign>
    <Check>
        <ServiceId>4321</ServiceId>
        <Account>1256789032</Account>
    </Check>
</Request>
EOD;
}

    public function XMLnorequest()
    {
        return <<<EOD
<NoRequest>
    <Sign></Sign>
    <Check>
        <ServiceId>1234</ServiceId>
        <Account>987654321</Account>
    </Check>
</NoRequest>
EOD;
    }

    public function test_constructor()
    {
        file_put_contents('php://input', $this->XMLcheck());
        $raw = new RAW();
        $r = new General($raw);

        $this->assertEquals(
            $raw,
            $this->invokeProperty($r, 'raw_request')->getValue($r)
        );
    }

    public function test_DateTime()
    {
        file_put_contents('php://input', $this->XMLcheck());
        $r = new General(new RAW());

        $this->assertEquals(
            $r->DateTime(),
            '2017-10-01T11:11:11'
        );
    }

    public function test_Sign()
    {
        file_put_contents('php://input', $this->XMLsign());
        $r = new General(new RAW());

        $this->assertEquals(
            $r->Sign(),
            '5F9ABA6AB64621F4DEDC42E8B90201A0D6203A725B54919719A2E05D323729CC5CD96D03115C31310FA90B38B953394537336A9B38C2DC2AB2367D85D04E1A19478B867A1BC03231FB78A7EA3C9FE74C7EE033CB3AB42159D639FE627953E8EF15C299F4A0902C96EF00F40677819FE5CF7A16B38544E70668024DA96F4AEE5E'
        );
    }

        public function test_Operation()
        {
            // Check
            file_put_contents('php://input', $this->XMLcheck());
            $r = new General(new RAW());

            $this->assertEquals(
                $r->Operation(),
                'Check'
            );

            // Payment
            file_put_contents('php://input', $this->XMLpayment());
            $r = new General(new RAW());

            $this->assertEquals(
                $r->Operation(),
                'Payment'
            );

            // Confirm
            file_put_contents('php://input', $this->XMLconfirm());
            $r = new General(new RAW());

            $this->assertEquals(
                $r->Operation(),
                'Confirm'
            );

            // Cancel
            file_put_contents('php://input', $this->XMLcancel());
            $r = new General(new RAW());

            $this->assertEquals(
                $r->Operation(),
                'Cancel'
            );
        }

        public function test_ServiceId()
        {
            file_put_contents('php://input', $this->XMLcheck());
            $r = new General(new RAW());

            $this->assertEquals(
                $r->ServiceId(),
                '1234'
            );
        }

        public function test_check_request_count()
        {
            file_put_contents('php://input', $this->XMLcheck());
            $r = new General(new RAW());

            $ar = array(1);

            $this->invokeMethod($r, 'check_request_count', array($ar));
        }

        /**
         * @expectedException EasyPay\Exception\Structure
         * @expectedExceptionCode -52
         * @expectedExceptionMessage The xml-query does not contain any element Request!
         */
        public function test_check_request_count_exception1()
        {
            file_put_contents('php://input', $this->XMLcheck());
            $r = new General(new RAW());

            $ar = array();

            $this->invokeMethod($r, 'check_request_count', array($ar));
        }

        /**
         * @expectedException EasyPay\Exception\Structure
         * @expectedExceptionCode -52
         * @expectedExceptionMessage The xml-query contains several elements Request!
         */
        public function test_check_request_count_exception2()
        {
            file_put_contents('php://input', $this->XMLcheck());
            $r = new General(new RAW());

            $ar = array(1,2,3);

            $this->invokeMethod($r, 'check_request_count', array($ar));
        }

        /**
         * @expectedException EasyPay\Exception\Structure
         * @expectedExceptionCode -52
         * @expectedExceptionMessage The xml-query does not contain any element Request!
         */
        public function test_validate_request_norequest()
        {
            $options = array(
                'ServiceId' => 1234
            );
            file_put_contents('php://input', $this->XMLnorequest());
            $r = new General(new RAW());
            $r->validate_request($options);
        }

        /**
         * @expectedException EasyPay\Exception\Structure
         * @expectedExceptionCode -57
         * @expectedExceptionMessage There is no DateTime element in the xml request!
         */
        public function test_validate_request_noDateTime()
        {
            $options = array(
                'ServiceId' => 1234
            );
            file_put_contents('php://input', <<<EOD
<Request>
  <Sign></Sign>
  <Check>
    <ServiceId>1234</ServiceId>
    <Account>987654321</Account>
  </Check>
</Request>
EOD
);
            $r = new General(new RAW());
            $r->validate_request($options);
        }

        /**
         * @expectedException EasyPay\Exception\Structure
         * @expectedExceptionCode -56
         * @expectedExceptionMessage There is more than one DateTime element in the xml-query!
         */
        public function test_validate_request_twoDateTime()
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
    <Account>987654321</Account>
  </Check>
  <DateTime>2017-10-01T11:11:11</DateTime>
</Request>
EOD
            );
            $r = new General(new RAW());
            $r->validate_request($options);
        }

        /**
         * @expectedException EasyPay\Exception\Structure
         * @expectedExceptionCode -57
         * @expectedExceptionMessage There is no Sign element in the xml request!
         */
        public function test_validate_request_noSign()
        {
            $options = array(
                'ServiceId' => 1234
            );
            file_put_contents('php://input', <<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Check>
    <ServiceId>1234</ServiceId>
    <Account>987654321</Account>
  </Check>
</Request>
EOD
            );
            $r = new General(new RAW());
            $r->validate_request($options);
        }

        /**
         * @expectedException EasyPay\Exception\Structure
         * @expectedExceptionCode -56
         * @expectedExceptionMessage There is more than one Sign element in the xml-query!
         */
        public function test_validate_request_twoSign()
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
    <Account>987654321</Account>
  </Check>
  <Sign></Sign>
</Request>
EOD
            );
            $r = new General(new RAW());
            $r->validate_request($options);
        }

        /**
         * @expectedException EasyPay\Exception\Structure
         * @expectedExceptionCode -55
         * @expectedExceptionMessage There is no Operation type element in the xml request!
         */
        public function test_validate_request_noOperation()
        {
            $options = array(
                'ServiceId' => 1234
            );
            file_put_contents('php://input', <<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
</Request>
EOD
            );
            $r = new General(new RAW());
            $r->validate_request($options);
        }

        /**
         * @expectedException EasyPay\Exception\Structure
         * @expectedExceptionCode -53
         * @expectedExceptionMessage There is more than one Operation type element in the xml-query!
         */
        public function test_validate_request_twoOperation()
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
    <Account>987654321</Account>
  </Check>
  <Cancel>
  </Cancel>
</Request>
EOD
            );
            $r = new General(new RAW());
            $r->validate_request($options);
        }

        /**
         * @expectedException EasyPay\Exception\Structure
         * @expectedExceptionCode -57
         * @expectedExceptionMessage There is no ServiceId element in the xml request!
         */
        public function test_validate_request_noServiceId()
        {
            $options = array(
                'ServiceId' => 1234
            );
            file_put_contents('php://input', <<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Check>
    <Account>987654321</Account>
  </Check>
</Request>
EOD
            );
            $r = new General(new RAW());
            $r->validate_request($options);
        }

        /**
         * @expectedException EasyPay\Exception\Structure
         * @expectedExceptionCode -56
         * @expectedExceptionMessage There is more than one ServiceId element in the xml-query!
         */
        public function test_validate_request_twoServiceId()
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
    <Account>987654321</Account>
    <ServiceId>5678</ServiceId>
  </Check>
</Request>
EOD
            );
            $r = new General(new RAW());
            $r->validate_request($options);
        }

        /**
         * @expectedException EasyPay\Exception\Data
         * @expectedExceptionCode -58
         * @expectedExceptionMessage This request is not for our ServiceId!
         */
        public function test_validate_request_invalidServiceId()
        {
            $options = array(
                'ServiceId' => 1234
            );
            file_put_contents('php://input', <<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Check>
    <ServiceId>2341</ServiceId>
    <Account>987654321</Account>
  </Check>
</Request>
EOD
            );
            $r = new General(new RAW());
            $r->validate_request($options);
        }

        public function test_validate_element()
        {
            file_put_contents('php://input', <<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Check>
  </Check>
</Request>
EOD
            );
            $r = new General(new RAW());
            $r->validate_element('DateTime');
        }

        /**
         * @expectedException EasyPay\Exception\Structure
         * @expectedExceptionCode -57
         * @expectedExceptionMessage There is no ServiceId element in the xml request!
         */
        public function test_validate_element_absent()
        {
            file_put_contents('php://input', <<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Check>
  </Check>
</Request>
EOD
            );
            $r = new General(new RAW());
            $r->validate_element('ServiceId');
        }

        public function test_verify_sign_noUseSignOption()
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
            $options = array(
                'ServiceId' => 1234
            );
            $r = new General(new RAW());
            $r->verify_sign($options);
        }

        public function test_verify_sign_falseUseSignOption()
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
            $options = array(
                'ServiceId' => 1234,
                'UseSign' => false,
            );
            $r = new General(new RAW());
            $r->verify_sign($options);
        }

        /**
         * @expectedException EasyPay\Exception\Sign
         * @expectedExceptionCode -95
         * @expectedExceptionMessage Signature of request is incorrect!
         */
        public function test_verify_sign_badsign()
        {
            file_put_contents('php://input', <<<EOD
<Request>
  <DateTime>2017-10-01T12:00:00</DateTime>
  <Sign>59DE8A1A2FBC5DA79E50718A98140F9A39AB597E2DEA7E8923DA9F249CFE023EE23A45994F7FEDD426760DFB82B56F2F2F4D0D5AACF54D3BBA8F2F103568C95D675BC278C08BD13E0867F13805A0967FA247D407F48C954F1D85EDC9A3108C64A7FB4F815C9566ACE6ABE817EBF40306893588D394FB12E599F81C2EA1805A8D</Sign>
  <Check>
    <ServiceId>1234</ServiceId>
    <Account>987654321</Account>
  </Check>
</Request>
EOD
            );
            $options = array(
                'ServiceId' => 1234,
                'UseSign' => true,
                'EasySoftPKey' => dirname(__FILE__).'/../../files/php_easypay.cer'
            );
            $r = new General(new RAW());
            $r->verify_sign($options);
        }

        public function test_verify_sign_oksign()
        {
            file_put_contents('php://input', <<<EOD
<Request>
  <DateTime>2017-10-01T12:00:00</DateTime>
  <Sign>59DE8A1A2FBC5DA79E50718A98140F9A39AB597E2DEA7E8923DA9F249CFE023EE23A45994F7FEDD426760DFB82B56F2F2F4D0D5AACF54D3BBA8F2F103568C95D675BC278C08BD13E0867F13805A0967FA247D407F48C954F1D85EDC9A3108C64A7FB4F815C9566ACE6ABE817EBF40306893588D394FB12E599F81C2EA1805A8D</Sign>
  <Check>
    <ServiceId>256</ServiceId>
    <Account>1024256</Account>
  </Check>
</Request>
EOD
            );
            $options = array(
                'ServiceId' => 256,
                'UseSign' => true,
                'EasySoftPKey' => dirname(__FILE__).'/../../files/php_easypay.cer'
            );
            $r = new General(new RAW());
            $r->verify_sign($options);
        }

}
