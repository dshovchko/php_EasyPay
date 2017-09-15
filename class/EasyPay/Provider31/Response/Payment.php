<?php

namespace EasyPay\Provider31\Response;

use \EasyPay\Provider31\Response as Response;

final class Payment extends Response
{
	function __construct($paymentid) {
		parent::__construct();
		
		$this->setElementValue('StatusCode', 0);
		$this->setElementValue('StatusDetail', 'checked');
		
		$this->create_PaymentId($paymentid);
	}
	
	public function create_PaymentId($paymentid)
	{
		$this->PaymentId = self::createElement('PaymentId', $paymentid);
		$this->Response->appendChild($this->PaymentId);
	}
}
