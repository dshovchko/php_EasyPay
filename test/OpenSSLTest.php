<?php

namespace EasyPayTest;

use \EasyPayTest\TestCase;
use \EasyPay\OpenSSL as OpenSSL;

class OpenSSLTest extends TestCase
{
    public function test_verify()
    {
        $raw =<<<EOD
<Request>
  <DateTime>2017-10-01T12:00:00</DateTime>
  <Sign></Sign>
  <Check>
    <ServiceId>256</ServiceId>
    <Account>1024256</Account>
  </Check>
</Request>
EOD;
        $sign = '59DE8A1A2FBC5DA79E50718A98140F9A39AB597E2DEA7E8923DA9F249CFE023EE23A45994F7FEDD426760DFB82B56F2F2F4D0D5AACF54D3BBA8F2F103568C95D675BC278C08BD13E0867F13805A0967FA247D407F48C954F1D85EDC9A3108C64A7FB4F815C9566ACE6ABE817EBF40306893588D394FB12E599F81C2EA1805A8D';

        $o = new OpenSSL();

        $this->assertEquals(
            1,
            $o->verify(
                $raw,
                pack("H*", $sign),
                $o->get_pub_key(file_get_contents(dirname(__FILE__).'/files/php_easypay.cer'))
            )
        );
    }

    public function test_verify_bad_sign()
    {
        $raw =<<<EOD
<Request>
  <DateTime>2017-10-01T12:00:00</DateTime>
  <Check>
    <ServiceId>256</ServiceId>
    <Account>1024256</Account>
  </Check>
</Request>
EOD;
        $sign = '759DE8A1A2FBC5DA79E50718A98140F9A39AB597E2DEA7E8923DA9F249CFE023EE23A45994F7FEDD426760DFB82B56F2F2F4D0D5AACF54D3BBA8F2F103568C95D675BC278C08BD13E0867F13805A0967FA247D407F48C94F1D85EDC9A3108C64A7FB4F815C9566ACE6ABE817EBF40306893588D394FB12E599F81C2EA1807777';

        $o = new OpenSSL();

        $this->assertEquals(
            0,
            $o->verify(
                $raw,
                pack("H*", $sign),
                $o->get_pub_key(file_get_contents(dirname(__FILE__).'/files/php_easypay.cer'))
            )
        );
    }

    public function test_sign()
    {
        $raw =<<<EOD
<Request>
  <DateTime>2017-10-01T12:00:00</DateTime>
  <Sign></Sign>
  <Check>
    <ServiceId>256</ServiceId>
    <Account>1024256</Account>
  </Check>
</Request>
EOD;
        $expected_sign = '59DE8A1A2FBC5DA79E50718A98140F9A39AB597E2DEA7E8923DA9F249CFE023EE23A45994F7FEDD426760DFB82B56F2F2F4D0D5AACF54D3BBA8F2F103568C95D675BC278C08BD13E0867F13805A0967FA247D407F48C954F1D85EDC9A3108C64A7FB4F815C9566ACE6ABE817EBF40306893588D394FB12E599F81C2EA1805A8D';

        $o = new OpenSSL();

        $this->assertEquals(
            TRUE,
            $o->sign(
                $raw,
                $sign,
                $o->get_priv_key(file_get_contents(dirname(__FILE__).'/files/php_easypay.ppk'))
            )
        );

        $this->assertEquals(
            $expected_sign,
            strtoupper(bin2hex($sign))
        );
    }

    public function test_get_pub_key()
    {
        $o = new OpenSSL();

        $ctx = file_get_contents(dirname(__FILE__).'/files/php_easypay.cer');
        $o->get_pub_key($ctx);
    }

    public function test_get_priv_key()
    {
        $o = new OpenSSL();

        $ctx = file_get_contents(dirname(__FILE__).'/files/php_easypay.ppk');
        $o->get_priv_key($ctx);
    }

    public function test_is_key()
    {
        $o = new OpenSSL();

        $ctx = file_get_contents(dirname(__FILE__).'/files/php_easypay.cer');
        $o->is_key(@openssl_pkey_get_public($ctx));
    }

    /**
     * @expectedException EasyPay\Exception\Runtime
     * @expectedExceptionCode -97
     * @expectedExceptionMessage Can not extract key from certificate!
     */
    public function test_is_key_bad()
    {
        $o = new OpenSSL();

        $o->is_key(false);
    }

}
