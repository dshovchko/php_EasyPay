<?php

	namespace EasyPay\Provider31\Response;
	
	use EasyPay\Provider31 as Provider31;
	
	final class ErrorInfo extends Provider31\Response
	{
		function __construct($code, $message) {
			parent::__construct();
			
			$this->setElementValue('StatusCode', $code);
			$this->setElementValue('StatusDetail', $message);
		}
	}
