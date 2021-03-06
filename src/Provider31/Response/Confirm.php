<?php

/**
 *      Class for response to Confirm request
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPay\Provider31\Response;

use \EasyPay\Provider31\Response as Response;

final class Confirm extends Response
{
    /**
     *      @var \DOMElement
     */
    protected $OrderDate;

	/**
     *      Confirm constructor
     *
     *      @param string $orderdate
     */
    public function __construct($orderdate)
    {
		parent::__construct();

		$this->setElementValue('StatusCode', 0);
		$this->setElementValue('StatusDetail', 'checked');

		$this->create_OrderDate($orderdate);
	}

	/**
     *      Create OrderDate node
     *
     *      @param string $orderdate
     */
	public function create_OrderDate($orderdate)
	{
		if (isset($this->OrderDate)) return;

		$this->OrderDate = $this->createElement('OrderDate', $orderdate);
		$this->Response->appendChild($this->OrderDate);
	}
}
