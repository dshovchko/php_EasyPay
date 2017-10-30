<?php

/**
 *      Test case
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPayTest;

use EasyPay\Log as Log;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    public function setUp()
    {
        Log::set(new StubLogger(''));
    }
    
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}