<?php

/**
 *      Test case for response
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPayTest;

use EasyPayTest\TestCase;

abstract class ResponseTestCase extends TestCase
{
    public function getXML($xml)
    {
        $dom = new \DOMDocument();
        $dom->loadXML($xml);
        $dom->encoding = 'UTF-8';
        $dom->formatOutput = true;
        
        return $dom->saveXML(NULL, LIBXML_NOEMPTYTAG);
    }
}