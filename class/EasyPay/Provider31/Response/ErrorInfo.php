<?php

	/**
	* @package EasyPay_Provider31
	*/
	final class EasyPay_Provider31_Response_ErrorInfo extends EasyPay_Provider31_Response
	{
		function __construct($code, $message) {
			parent::__construct();
			
			$this->setElementValue('StatusCode', $code);
			$this->setElementValue('StatusDetail', $message);
		}
	}
?>
