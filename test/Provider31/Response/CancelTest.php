<?php

/**
 *      Class for response to Cancel request
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPayTest\Provider31\Response;

use EasyPayTest\ResponseTestCase;
use EasyPay\Provider31\Response\Cancel;

final class CancelTest extends ResponseTestCase
{
	
	public function test_construct()
	{
		$cancel = new Cancel('2017-10-01T12:00:00');
		$cancel->setElementValue('DateTime',NULL);
		$this->assertEquals(
			$cancel->friendly(),
			$this->getXML('<Response><StatusCode>0</StatusCode><StatusDetail>checked</StatusDetail><DateTime></DateTime><CancelDate>2017-10-01T12:00:00</CancelDate></Response>')
		);
	}
	
	public function test_doubleCancelDate()
	{
		$cancel = new Cancel('2017-10-01T12:00:00');
		$cancel->setElementValue('DateTime',NULL);
		$cancel->create_CancelDate('2017-11-01T12:00:00');
		$this->assertEquals(
			$cancel->friendly(),
			$this->getXML('<Response><StatusCode>0</StatusCode><StatusDetail>checked</StatusDetail><DateTime></DateTime><CancelDate>2017-10-01T12:00:00</CancelDate></Response>')
		);
	}
}
