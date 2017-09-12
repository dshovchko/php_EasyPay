<?php

	/**
	* @package EasyPay_Provider31
	*/
	final class EasyPay_Provider31_Response_Check extends EasyPay_Provider31_Response
	{
		function __construct(EasyPay_Provider31_AccountInfo $accountinfo) {
			parent::__construct();
			
			$this->setElementValue('StatusCode', 0);
			$this->setElementValue('StatusDetail', 'checked');
			
			$this->create_AccountInfo($accountinfo);
		}
		
		public function create_AccountInfo($accountinfo)
		{
			$this->AccountInfo = self::createElement('AccountInfo');
			$this->Response->appendChild($this->AccountInfo);
			
			foreach($accountinfo as $parameter=>$value)
			{
				$this->AccountInfo->appendChild(self::createElement($parameter, $value));
			}
		}
	}
