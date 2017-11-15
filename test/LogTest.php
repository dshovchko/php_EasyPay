<?php

/**
 *      Singleton class for logging
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPayTest;

use EasyPayTest\TestCase;
use EasyPay\Log;

class LogTest extends TestCase
{
        public function setUp() {}

        public function test_set_instance()
        {
                $logger = new StubLogger('test');
                Log::set($logger);

                $this->assertEquals(
                        $logger,
                        Log::instance()
                    );
        }
}
