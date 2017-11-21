<?php

namespace EasyPayTest;

use \EasyPayTest\TestCase;
use \EasyPay\Key as Key;

class KeyTest extends TestCase
{
    public function test_get()
    {
        $k = new Key();
        $this->assertEquals(
            file_get_contents(dirname(__FILE__).'/files/php_easypay.cer'),
            $k->get(dirname(__FILE__).'/files/php_easypay.cer', 'public')
        );
    }

    /**
     * @expectedException EasyPay\Exception\Runtime
     * @expectedExceptionMessage not exists
     */
    public function test_get_notexists()
    {
        $k = new Key();
        $k->get(dirname(__FILE__).'/files/php_easypay.notexists', 'private');
    }

    public function test_check_exists()
    {
        $k = new Key();
        $this->invokeMethod($k, 'check_exists', array(dirname(__FILE__).'/files/php_easypay.cer'));
    }

    /**
     * @expectedException EasyPay\Exception\Runtime
     * @expectedExceptionMessage not exists
     */
    public function test_check_exists_not()
    {
        $k = new Key();
        $this->invokeMethod($k, 'check_exists', array(dirname(__FILE__).'/files/php_easypay.notexist'));
    }

    public function test_load()
    {
        $k = new Key();
        $this->assertEquals(
            file_get_contents(dirname(__FILE__).'/files/php_easypay.ppk'),
            $this->invokeMethod($k, 'load', array(dirname(__FILE__).'/files/php_easypay.ppk'))
        );
    }

    /**
     * @expectedException EasyPay\Exception\Runtime
     * @expectedExceptionMessage not read
     */
    public function test_load_not()
    {
        $k = new Key();
        $this->invokeMethod($k, 'load', array(dirname(__FILE__).'/files/php_easypay.notexists'));
    }
}
