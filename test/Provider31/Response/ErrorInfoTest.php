<?php

/**
 *      Class for error response
 *
 *      @package php_EasyPay
 *      @version 1.0
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPayTest\Provider31\Response;

use EasyPayTest\ResponseTestCase;
use EasyPay\Provider31\Response\ErrorInfo;

final class ErrorInfoTest extends ResponseTestCase
{
	
	public function test_construct()
	{
		$check = new ErrorInfo(1, 'eRRoR');
		$check->setElementValue('DateTime',NULL);
		$this->assertEquals(
			$check->friendly(),
			$this->getXML('<Response><StatusCode>1</StatusCode><StatusDetail>eRRoR</StatusDetail><DateTime></DateTime></Response>')
		);
	}
}
