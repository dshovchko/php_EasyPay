<?php

/**
 *      Class for response to Check request
 *
 *      @package php_EasyPay
 *      @version 1.0
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPayTest\Provider31\Response;

use EasyPayTest\ResponseTestCase;
use EasyPay\Provider31\AccountInfo;
use EasyPay\Provider31\Response\Check;

final class CheckTest extends ResponseTestCase
{
	
	public function test_construct()
	{
		$accountinfo = new AccountInfo(array(
			'Account' => 1,
                        'Fio' => 'fIO',
                        'Address' => 'aDDRESS',
		));
		$check = new Check($accountinfo);
		$check->setElementValue('DateTime',NULL);
		$this->assertEquals(
			$check->friendly(),
			$this->getXML('<Response><StatusCode>0</StatusCode><StatusDetail>checked</StatusDetail><DateTime></DateTime><AccountInfo><Account>1</Account><Fio>fIO</Fio><Address>aDDRESS</Address></AccountInfo></Response>')
		);
	}
	
	public function test_doubleAccountInfo()
	{
		$accountinfo = new AccountInfo(array(
			'Account' => 1,
                        'Fio' => 'fIO',
                        'Address' => 'aDDRESS',
		));
		$check = new Check($accountinfo);
		$check->setElementValue('DateTime',NULL);
		$check->create_AccountInfo($accountinfo);
		$this->assertEquals(
			$check->friendly(),
			$this->getXML('<Response><StatusCode>0</StatusCode><StatusDetail>checked</StatusDetail><DateTime></DateTime><AccountInfo><Account>1</Account><Fio>fIO</Fio><Address>aDDRESS</Address></AccountInfo></Response>')
		);
	}
}
