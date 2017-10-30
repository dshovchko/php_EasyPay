<?php

/**
 *      Class for response to Payment request
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPay\Provider31\Response;

use \EasyPay\Provider31\Response as Response;

final class Payment extends Response
{
	/**
         *      Payment constructor
         *
         *      @param string $paymentid
         */
	function __construct($paymentid) {
		parent::__construct();
		
		$this->setElementValue('StatusCode', 0);
		$this->setElementValue('StatusDetail', 'checked');
		
		$this->create_PaymentId($paymentid);
	}
	
	/**
         *      Create PaymentId node
         *
         *      @param string $paymentid
         */
	public function create_PaymentId($paymentid)
	{
		if (isset($this->PaymentId)) return;
		
		$this->PaymentId = self::createElement('PaymentId', $paymentid);
		$this->Response->appendChild($this->PaymentId);
	}
}
