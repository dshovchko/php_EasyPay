<?php

namespace EasyPay\Provider31\Response;

use \EasyPay\Provider31\Response as Response;
use \EasyPay\Provider31\AccountInfo as AccountInfo;

final class Check extends Response
{
	function __construct(AccountInfo $accountinfo) {
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
