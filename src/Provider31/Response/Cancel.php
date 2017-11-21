<?php

/**
 *      Class for response to Cancel request
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPay\Provider31\Response;

use \EasyPay\Provider31\Response as Response;

final class Cancel extends Response
{
    /**
     *      @var \DOMElement
     */
    protected $CancelDate;

	/**
     *      Cancel constructor
     *
     *      @param string $orderdate
     */
	public function __construct($canceldate) {
		parent::__construct();

		$this->setElementValue('StatusCode', 0);
		$this->setElementValue('StatusDetail', 'checked');

		$this->create_CancelDate($canceldate);
	}

	/**
     *      Create CancelDate node
     *
     *      @param string $canceldate
     */
	public function create_CancelDate($canceldate)
	{
		if (isset($this->CancelDate)) return;

		$this->CancelDate = self::createElement('CancelDate', $canceldate);
		$this->Response->appendChild($this->CancelDate);
	}
}
