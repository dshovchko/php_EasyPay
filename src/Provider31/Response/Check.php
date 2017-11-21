<?php

/**
 *      Class for response to Check request
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPay\Provider31\Response;

use \EasyPay\Provider31\Response as Response;
use \EasyPay\Provider31\AccountInfo as AccountInfo;

final class Check extends Response
{
    /**
     *      @var string
     */
    protected $AccountInfo;

	/**
     *      Check constructor
     *
     *      @param AccountInfo $accountinfo account information set
     */
    public function __construct(AccountInfo $accountinfo)
    {
		parent::__construct();

		$this->setElementValue('StatusCode', 0);
		$this->setElementValue('StatusDetail', 'checked');

		$this->create_AccountInfo($accountinfo);
	}

	/**
     *      Create AccountInfo node with child nodes
     *
     *      @param AccountInfo $accountinfo account information set
     */
	public function create_AccountInfo($accountinfo)
	{
		if (isset($this->AccountInfo)) return;

		$this->AccountInfo = self::createElement('AccountInfo');
		$this->Response->appendChild($this->AccountInfo);

		foreach($accountinfo as $parameter=>$value)
		{
			$this->AccountInfo->appendChild(self::createElement($parameter, $value));
		}
	}
}
