<?php

namespace EasyPay\Provider31\Response;

use \EasyPay\Provider31\Response as Response;

final class ErrorInfo extends Response
{
	function __construct($code, $message) {
		parent::__construct();
		
		$this->setElementValue('StatusCode', $code);
		$this->setElementValue('StatusDetail', $message);
	}
}
