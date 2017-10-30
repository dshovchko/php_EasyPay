<?php

/**
 *      Class for response to Payment request
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPayTest\Provider31\Response;

use EasyPayTest\ResponseTestCase;
use EasyPay\Provider31\Response\Payment;

final class PaymentTest extends ResponseTestCase
{
	
	public function test_construct()
	{
		$payment = new Payment('1234567890');
		$payment->setElementValue('DateTime',NULL);
		$this->assertEquals(
			$payment->friendly(),
			$this->getXML('<Response><StatusCode>0</StatusCode><StatusDetail>checked</StatusDetail><DateTime></DateTime><PaymentId>1234567890</PaymentId></Response>')
		);
	}
	
	public function test_doublePaymentId()
	{
		$payment = new Payment('1234567890');
		$payment->setElementValue('DateTime',NULL);
		$payment->create_PaymentId('987');
		$this->assertEquals(
			$payment->friendly(),
			$this->getXML('<Response><StatusCode>0</StatusCode><StatusDetail>checked</StatusDetail><DateTime></DateTime><PaymentId>1234567890</PaymentId></Response>')
		);
	}
}
