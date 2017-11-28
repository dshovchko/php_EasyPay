<?php

/**
 *      Class for RAW request
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPayTest\Provider31;

use EasyPayTest\TestCase;
use EasyPay\Provider31\Request\RAW;

class RAWTest extends TestCase
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

    public function XML1()
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

    public function XML2()
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

    public function XMLbad()
    {
        return <<<EOD
<Request>
    <DateTime>2017-10-16T15:12:32</DateTime>
    <Sign></Sign>
EOD;
    }

    public function XMLcomplex()
    {
        return <<<EOD
<Request>
    <DateTime>2017-10-01T11:11:11</DateTime>
    <Sign></Sign>
    <Check>
        <ServiceId>1234</ServiceId>
        <Account>987654321</Account>
    </Check>
    <Payment>
        <ServiceId>4321</ServiceId>
        <OrderId>43232128933</OrderId>
        <Account>1256789032</Account>
        <Amount>10</Amount>
        <Check>
            <ServiceId>1234</ServiceId>
            <Account>987654321</Account>
        </Check>
    </Payment>
</Request>
EOD;
    }

    public function test_str()
    {
        file_put_contents('php://input', $this->XML1());
        $r = new RAW();

        $this->assertEquals(
            $this->XML1(),
            $r->str()
        );
    }

    public function test_get_http_raw_post_data()
    {
        file_put_contents('php://input', $this->XML1());
        $r = new RAW();
        file_put_contents('php://input', $this->XML2());
        $this->invokeMethod($r, 'get_http_raw_post_data', array(null));

        $this->assertEquals(
            $this->XML2(),
            $this->invokeProperty($r, 'raw_request')->getValue($r)
        );
    }

    public function test_check_presence_request()
    {
        file_put_contents('php://input', $this->XML1());
        $r = new RAW();

        $this->invokeMethod($r, 'check_presence_request', array(null));
    }

    /**
     * @expectedException EasyPay\Exception\Structure
     * @expectedExceptionCode -50
     * @expectedExceptionMessage An empty xml request
     */
    public function test_check_presence_request_exception()
    {
        file_put_contents('php://input', $this->XML1());
        $r = new RAW();

        $this->invokeProperty($r, 'raw_request')->setValue($r, null);

        $this->invokeMethod($r, 'check_presence_request', array(null));
    }

    public function test_get_nodes_from_request()
    {
        file_put_contents('php://input', $this->XML1());
        $r = new RAW();

        $doc = new \DOMDocument();
        $doc->loadXML($this->XML1());
        $check = $this->invokeMethod($r, 'getNodes', array($doc, 'Check'));

        $this->assertEquals(
            $check,
            $r->get_nodes_from_request('Check')
        );
    }

    /**
     * @expectedException EasyPay\Exception\Structure
     * @expectedExceptionCode -51
     * @expectedExceptionMessage The wrong XML is received
     */
    public function test_get_nodes_from_request_exception()
    {
        file_put_contents('php://input', $this->XMLbad());
        $r = new RAW();
        $r->get_nodes_from_request('Check');
    }

    public function get_nodes($dom, $name, $ret=array())
    {
        foreach($dom->childNodes as $child)
        {
            if ($child->nodeName == $name)
            {
                array_push($ret, $child);
            }
            else
            {
                if (count($child->childNodes) > 0)
                {
                    $ret = $this->get_nodes($child, $name, $ret);
                }
            }
        }

        return $ret;
    }

    public function test_get_nodes()
    {
        file_put_contents('php://input', $this->XML1());
        $r = new RAW();

        $doc = new \DOMDocument();
        $doc->loadXML($this->XML1());
        $check = $this->get_nodes($doc, 'Check');

        $this->assertEquals(
            $check,
            $this->invokeMethod($r, 'getNodes', array($doc, 'Check'))
        );
    }
}
