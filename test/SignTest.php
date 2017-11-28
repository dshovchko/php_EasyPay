<?php

/**
 *      General class for all request types
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPayTest;

use EasyPayTest\TestCase;
use EasyPay\Sign;

class SignTest extends TestCase
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

    public function test_verify_sign_noUseSignOption()
    {
        $raw = <<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Check>
    <ServiceId>1234</ServiceId>
    <Account>987654321</Account>
  </Check>
</Request>
EOD;

        $sign = '';
        $options = array(
            'ServiceId' => 1234
        );
        $s = new Sign();
        $s->verify($raw, $sign, $options);
    }

        public function test_verify_sign_falseUseSignOption()
        {
            $raw = <<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Check>
    <ServiceId>1234</ServiceId>
    <Account>987654321</Account>
  </Check>
</Request>
EOD;

            $sign = '';
            $options = array(
                'ServiceId' => 1234,
                'UseSign' => false,
            );
            $s = new Sign();
            $s->verify($raw, $sign, $options);
        }

        /**
         * @expectedException EasyPay\Exception\Runtime
         * @expectedExceptionCode -94
         * @expectedExceptionMessage The parameter EasySoftPKey is not set!
         */
        public function test_verify_sign_noEasySoftPKey()
        {
            $raw = <<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Check>
    <ServiceId>1234</ServiceId>
    <Account>987654321</Account>
  </Check>
</Request>
EOD;

            $sign = '';
            $options = array(
                'ServiceId' => 1234,
                'UseSign' => true,
            );
            $s = new Sign();
            $s->verify($raw, $sign, $options);
        }

        /**
         * @expectedException EasyPay\Exception\Runtime
         * @expectedExceptionCode -98
         * @expectedExceptionMessage The file with the public key was not exists!
         */
        public function test_verify_sign_badfileEasySoftPKey()
        {
            $raw = <<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Check>
    <ServiceId>1234</ServiceId>
    <Account>987654321</Account>
  </Check>
</Request>
EOD;

            $sign = '';
            $options = array(
                'ServiceId' => 1234,
                'UseSign' => true,
                'EasySoftPKey' => '/foo.key'
            );
            $s = new Sign();
            $s->verify($raw, $sign, $options);
        }

        /**
         * @expectedException EasyPay\Exception\Runtime
         * @expectedExceptionCode -97
         * @expectedExceptionMessage Can not extract key from certificate!
         */
        public function test_verify_sign_corruptedfileEasySoftPKey()
        {
            $raw = <<<EOD
<Request>
  <DateTime>2017-10-01T11:11:11</DateTime>
  <Sign></Sign>
  <Check>
    <ServiceId>1234</ServiceId>
    <Account>987654321</Account>
  </Check>
</Request>
EOD;

            $sign = '';
            $options = array(
                'ServiceId' => 1234,
                'UseSign' => true,
                'EasySoftPKey' => dirname(__FILE__).'/files/php_easypay_corrupted.cer'
            );
            $s = new Sign();
            $s->verify($raw, $sign, $options);
        }

        /**
         * @expectedException EasyPay\Exception\Sign
         * @expectedExceptionCode -95
         * @expectedExceptionMessage Signature of request is incorrect!
         */
        public function test_verify_sign_badsign()
        {
            $raw = <<<EOD
<Request>
  <DateTime>2017-10-01T12:00:00</DateTime>
  <Sign>59DE8A1A2FBC5DA79E50718A98140F9A39AB597E2DEA7E8923DA9F249CFE023EE23A45994F7FEDD426760DFB82B56F2F2F4D0D5AACF54D3BBA8F2F103568C95D675BC278C08BD13E0867F13805A0967FA247D407F48C954F1D85EDC9A3108C64A7FB4F815C9566ACE6ABE817EBF40306893588D394FB12E599F81C2EA1805A8D</Sign>
  <Check>
    <ServiceId>1234</ServiceId>
    <Account>987654321</Account>
  </Check>
</Request>
EOD;

            $sign = '59DE8A1A2FBC5DA79E50718A98140F9A39AB597E2DEA7E8923DA9F249CFE023EE23A45994F7FEDD426760DFB82B56F2F2F4D0D5AACF54D3BBA8F2F103568C95D675BC278C08BD13E0867F13805A0967FA247D407F48C954F1D85EDC9A3108C64A7FB4F815C9566ACE6ABE817EBF40306893588D394FB12E599F81C2EA1805A8D';
            $options = array(
                'ServiceId' => 1234,
                'UseSign' => true,
                'EasySoftPKey' => dirname(__FILE__).'/files/php_easypay.cer'
            );
            $s = new Sign();
            $s->verify($raw, $sign, $options);
        }

        public function test_verify_sign_oksign()
        {
            $raw = <<<EOD
<Request>
  <DateTime>2017-10-01T12:00:00</DateTime>
  <Sign>59DE8A1A2FBC5DA79E50718A98140F9A39AB597E2DEA7E8923DA9F249CFE023EE23A45994F7FEDD426760DFB82B56F2F2F4D0D5AACF54D3BBA8F2F103568C95D675BC278C08BD13E0867F13805A0967FA247D407F48C954F1D85EDC9A3108C64A7FB4F815C9566ACE6ABE817EBF40306893588D394FB12E599F81C2EA1805A8D</Sign>
  <Check>
    <ServiceId>256</ServiceId>
    <Account>1024256</Account>
  </Check>
</Request>
EOD;

            $sign = '59DE8A1A2FBC5DA79E50718A98140F9A39AB597E2DEA7E8923DA9F249CFE023EE23A45994F7FEDD426760DFB82B56F2F2F4D0D5AACF54D3BBA8F2F103568C95D675BC278C08BD13E0867F13805A0967FA247D407F48C954F1D85EDC9A3108C64A7FB4F815C9566ACE6ABE817EBF40306893588D394FB12E599F81C2EA1805A8D';
            $options = array(
                'ServiceId' => 256,
                'UseSign' => true,
                'EasySoftPKey' => dirname(__FILE__).'/files/php_easypay.cer'
            );
            $s = new Sign();
            $s->verify($raw, $sign, $options);
        }

        public function test_get_pub_key()
        {
            $raw = <<<EOD
<Request>
  <DateTime>2017-10-01T12:00:00</DateTime>
  <Sign></Sign>
  <Check>
    <ServiceId>256</ServiceId>
    <Account>1024256</Account>
  </Check>
</Request>
EOD;

            $options = array(
                'ServiceId' => 256,
                'UseSign' => true,
                'EasySoftPKey' => dirname(__FILE__).'/files/php_easypay.cer'
            );
            $s = new Sign();

            $this->assertEquals(
                file_get_contents(dirname(__FILE__).'/files/php_easypay.cer'),
                $this->invokeMethod($s, 'get_pub_key', array($options))
            );
        }

        /**
         * @expectedException EasyPay\Exception\Runtime
         * @expectedExceptionCode -94
         * @expectedExceptionMessage The parameter EasySoftPKey is not set!
         */
        public function test_get_pub_key_exception()
        {
            $raw = <<<EOD
<Request>
  <DateTime>2017-10-01T12:00:00</DateTime>
  <Sign></Sign>
  <Check>
    <ServiceId>256</ServiceId>
    <Account>1024256</Account>
  </Check>
</Request>
EOD;

            $options = array(
                'ServiceId' => 256,
                'UseSign' => true,
            );
            $s = new Sign();

            $this->invokeMethod($s, 'get_pub_key', array($options));
        }

        public function test_check_verify_sign_result()
        {
            $raw = <<<EOD
<Request>
        <DateTime>2017-10-01T11:11:11</DateTime>
        <Sign></Sign>
        <Check>
        </Check>
</Request>
EOD;

            $s = new Sign();
            $this->invokeMethod($s, 'check_verify_sign_result', array(1));
        }

        /**
         * @expectedException EasyPay\Exception\Sign
         * @expectedExceptionCode -95
         * @expectedExceptionMessage Signature of request is incorrect!
         */
        public function test_check_verify_sign_result_exception_95()
        {
            $raw = <<<EOD
<Request>
        <DateTime>2017-10-01T11:11:11</DateTime>
        <Sign></Sign>
        <Check>
        </Check>
</Request>
EOD;

            $s = new Sign();
            $this->invokeMethod($s, 'check_verify_sign_result', array(0));
        }

        /**
         * @expectedException EasyPay\Exception\Sign
         * @expectedExceptionCode -96
         * @expectedExceptionMessage Error verify signature of request!
         */
        public function test_check_verify_sign_result_exception_96()
        {
            $raw = <<<EOD
<Request>
        <DateTime>2017-10-01T11:11:11</DateTime>
        <Sign></Sign>
        <Check>
        </Check>
</Request>
EOD;

            $s = new Sign();
            $this->invokeMethod($s, 'check_verify_sign_result', array(-1));
        }

        public function test_generate_null()
        {
            $s = new Sign();

            $xml = '<Response><StatusCode></StatusCode><StatusDetail></StatusDetail><DateTime>2017-07-28T12:36:07</DateTime></Response>';
            // without options
            $options = array();
            $this->assertNULL($s->generate($xml, $options));
        }

        public function test_generate_emptyPKey()
        {
            $s = new Sign();

            $xml = '<Response><StatusCode></StatusCode><StatusDetail></StatusDetail><DateTime>2017-07-28T12:36:07</DateTime></Response>';
            $options = array(
                'ProviderPKey' => '',
            );
            $this->assertNULL($s->generate($xml, $options));
        }

        public function test_generate_wrongPKey()
        {
            $s = new Sign();

            $xml = '<Response><StatusCode></StatusCode><StatusDetail></StatusDetail><DateTime>2017-07-28T12:36:07</DateTime></Response>';
            // with wrong ProviderPKey
            $options = array(
                'ProviderPKey' => '/foobaz.key',
            );
            $this->assertNULL($s->generate($xml, $options));
        }

        public function test_generate_ok()
        {
            $s = new Sign();

            $xml = '<Response><StatusCode></StatusCode><StatusDetail></StatusDetail><DateTime>2017-07-28T12:36:07</DateTime></Response>';
            // with right ProviderPKey
            $options = array(
                'ProviderPKey' => dirname(__FILE__).'/files/php_easypay.ppk',
            );
            $this->assertEquals(
                '6F27518C3348B06CF06B722B873413D332C74E70E7F6B76808BB4E42E2B95E2B4422EA2DE68D9E731B97192C1405F23141DCF78E37EEA7362D16A5AD6207241C36F9E1DCAA49AB4E6A3686EC6C154AFEF69651BB149827374168DA24EA6C7522BD202879E6383E89D27D7CFA036D9282E6FC63345063C46747094386D1E639FF',
                $s->generate($xml, $options)
            );
        }

        public function test_generate_with_bad_key()
        {
            $s = new Sign();

            $xml = '<Response><StatusCode></StatusCode><StatusDetail></StatusDetail><DateTime>2017-07-28T12:36:07</DateTime></Response>';

            $options = array(
                'ProviderPKey' => dirname(__FILE__).'/files/php_easypay_corrupted.cer',
            );
            $this->assertNULL($s->generate($xml, $options));
        }

        public function test_get_priv_key()
        {
            $s = new Sign();

            $options = array(
                'ProviderPKey' => dirname(__FILE__).'/files/php_easypay.ppk',
            );
            $this->assertEquals(
                file_get_contents(dirname(__FILE__).'/files/php_easypay.ppk'),
                $this->invokeMethod($s, 'get_priv_key', array($options))
            );
        }

        /**
         * @expectedException EasyPay\Exception\Runtime
         * @expectedExceptionCode -94
         * @expectedExceptionMessage The parameter ProviderPKey is not set!
         */
        public function test_get_priv_key_exception()
        {
            $s = new Sign();

            $options = array();
            $this->assertEquals(
                file_get_contents(dirname(__FILE__).'/files/php_easypay.ppk'),
                $this->invokeMethod($s, 'get_priv_key', array($options))
            );
        }

        public function test_check_generate_sign_result()
        {
            $s = new Sign();

            $this->invokeMethod($s, 'check_generate_sign_result', array(true));
        }

        /**
         * @expectedException EasyPay\Exception\Sign
         * @expectedExceptionCode -96
         * @expectedExceptionMessage Can not generate signature!
         */
        public function test_check_generate_sign_result_exception()
        {
            $s = new Sign();

            $this->invokeMethod($s, 'check_generate_sign_result', array(false));
        }
}
