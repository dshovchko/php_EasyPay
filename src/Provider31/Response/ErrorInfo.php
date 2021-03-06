<?php

/**
 *      Class for error response
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPay\Provider31\Response;

use \EasyPay\Provider31\Response as Response;

final class ErrorInfo extends Response
{
	/**
     *      ErrorInfo constructor
     *
     *      @param integer $code Error code
     *      @param string $message Error message text
     */
    public function __construct($code, $message)
    {
		parent::__construct();

		$this->setElementValue('StatusCode', $code);
		$this->setElementValue('StatusDetail', $message);
	}
}
