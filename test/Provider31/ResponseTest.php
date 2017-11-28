<?php

/**
 *      Abstract class for the family of response classes
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPayTest\Provider31;

use EasyPayTest\TestCase;
use EasyPayTest\ResponseTestCase;
use EasyPay\Provider31\Response;

class ResponseStub extends Response
{
    public function set_DateTime()
    {
        $this->setElementValue('DateTime', '2017-07-28T12:36:07');
    }
}

class ResponseTest extends ResponseTestCase
{
    public function test_constructor()
    {
        $stub = new ResponseStub();

        $this->assertEquals(
            $this->getXML('<Response><StatusCode></StatusCode><StatusDetail></StatusDetail><DateTime>2017-07-28T12:36:07</DateTime></Response>'),
            $stub->friendly()
        );
    }

    public function test_set_DateTime()
    {
        $stub = $this->getMockForAbstractClass('\EasyPay\Provider31\Response');
        $stub->set_DateTime();

        $this->assertEquals(
            $this->getXML('<Response><StatusCode></StatusCode><StatusDetail></StatusDetail><DateTime>'.date('Y-m-d\TH:i:s', time()).'</DateTime></Response>'),
            $stub->friendly()
        );
    }

    public function test_setElementValue()
    {
        $stub = new ResponseStub();

        $this->assertEquals(
            $this->getXML('<Response><StatusCode></StatusCode><StatusDetail></StatusDetail><DateTime>2017-07-28T12:36:07</DateTime></Response>'),
            $stub->friendly()
        );

        $stub->setElementValue('StatusCode', 'qwerty');

        $this->assertEquals(
            $this->getXML('<Response><StatusCode>qwerty</StatusCode><StatusDetail></StatusDetail><DateTime>2017-07-28T12:36:07</DateTime></Response>'),
            $stub->friendly()
        );
    }

    public function test_friendly()
    {
        $stub = new ResponseStub();

        $this->assertEquals(
            $this->getXML('<Response><StatusCode></StatusCode><StatusDetail></StatusDetail><DateTime>2017-07-28T12:36:07</DateTime></Response>'),
            $stub->friendly()
        );
    }

    public function test_sign()
    {
        $stub = $this->getMockForAbstractClass('\EasyPay\Provider31\Response');
        $stub->setElementValue('DateTime', '2017-10-01T12:00:00');

        // without options
        $options = array();
        $state = $this->invokeMethod($stub, 'sign', array($options));
        $this->assertEquals(
            $this->getXML('<Response><StatusCode></StatusCode><StatusDetail></StatusDetail><DateTime>2017-10-01T12:00:00</DateTime></Response>'),
            $stub->friendly()
        );

        // usesign - false
        $options = array(
            'UseSign' => false
        );
        $state = $this->invokeMethod($stub, 'sign', array($options));
        $this->assertEquals(
            $this->getXML('<Response><StatusCode></StatusCode><StatusDetail></StatusDetail><DateTime>2017-10-01T12:00:00</DateTime></Response>'),
            $stub->friendly()
        );

        // usesign - true, but without ProviderPKey
        $options = array(
            'UseSign' => true
        );
        $state = $this->invokeMethod($stub, 'sign', array($options));
        $this->assertEquals(
            $this->getXML('<Response><StatusCode></StatusCode><StatusDetail></StatusDetail><DateTime>2017-10-01T12:00:00</DateTime><Sign></Sign></Response>'),
            $stub->friendly()
        );

        // usesign - true, and ProviderPKey is set  BUT!!! previously set empty Sign
        $options = array(
            'UseSign' => true,
            'ProviderPKey' => dirname(__FILE__).'/../files/php_easypay.ppk'
        );
        $state = $this->invokeMethod($stub, 'sign', array($options));
        $this->assertEquals(
            $this->getXML('<Response><StatusCode></StatusCode><StatusDetail></StatusDetail><DateTime>2017-10-01T12:00:00</DateTime><Sign></Sign></Response>'),
            $stub->friendly()
        );

        // usesign - true, and ProviderPKey is set  BUT!!! previously set empty Sign
        $stub = $this->getMockForAbstractClass('\EasyPay\Provider31\Response');
        $stub->setElementValue('DateTime', '2017-10-01T12:00:00');
        $options = array(
            'UseSign' => true,
            'ProviderPKey' => dirname(__FILE__).'/../files/php_easypay.ppk'
        );
        $state = $this->invokeMethod($stub, 'sign', array($options));
        $this->assertEquals(
            $this->getXML('<Response><StatusCode></StatusCode><StatusDetail></StatusDetail><DateTime>2017-10-01T12:00:00</DateTime><Sign>6E7AA942FD01150E1F1D2A73E60329F5AA826F0BA379CAF040CB659D927FFBF26DDFFFD5C24AA2BA202EAF6FA3E32169937A076395B7D86CCFF651DF2DC3BB2DC5937F99113A919B73FA54FECCDA2D4791C4264518BC77C3922AE61C83FFB2BE7022C04A914046560300B52D9B306A28A37C5B8186C1C91739532CB9C60D8603</Sign></Response>'),
            $stub->friendly()
        );
    }

    public function test_out()
    {
        $stub = new ResponseStub();
        $raw =<<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<Response>
  <StatusCode></StatusCode>
  <StatusDetail></StatusDetail>
  <DateTime>2017-07-28T12:36:07</DateTime>
</Response>

EOD;

        $this->expectOutputString($raw);

        $stub->sign_and_out(array());
    }
}
