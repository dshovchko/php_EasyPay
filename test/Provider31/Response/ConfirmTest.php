<?php

/**
 *      Class for response to Confirm request
 *
 *      @package php_EasyPay
 *      @version 1.0
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPayTest\Provider31\Response;

use EasyPayTest\ResponseTestCase;
use EasyPay\Provider31\Response\Confirm;

final class ConfirmTest extends ResponseTestCase
{
	
	public function test_construct()
	{
		$confirm = new Confirm('2017-10-01T12:00:00');
		$confirm->setElementValue('DateTime',NULL);
		$this->assertEquals(
			$confirm->friendly(),
			$this->getXML('<Response><StatusCode>0</StatusCode><StatusDetail>checked</StatusDetail><DateTime></DateTime><OrderDate>2017-10-01T12:00:00</OrderDate></Response>')
		);
	}
	
	public function test_doubleOrderDate()
	{
		$confirm = new Confirm('2017-10-01T12:00:00');
		$confirm->setElementValue('DateTime',NULL);
		$confirm->create_OrderDate('2017-11-01T12:00:00');
		$this->assertEquals(
			$confirm->friendly(),
			$this->getXML('<Response><StatusCode>0</StatusCode><StatusDetail>checked</StatusDetail><DateTime></DateTime><OrderDate>2017-10-01T12:00:00</OrderDate></Response>')
		);
	}
}
