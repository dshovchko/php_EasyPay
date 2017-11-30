<?php

/**
 *      Class for Payment request
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPay\Provider31\Request;

use EasyPay\Log as Log;
use EasyPay\Exception;

class Payment extends General
{
    /**
     *      @var string 'Account' node
     */
    protected $Account;

    /**
     *      @var string 'OrderId' node
     */
    protected $OrderId;

    /**
     *      @var string 'Amount' node
     */
    protected $Amount;

    /**
     *      Payment constructor
     *
     *      @param \EasyPay\Provider31\Request\RAW $raw Raw request data
     */
    public function __construct($raw)
    {
        parent::__construct($raw);
    }

    /**
     *      Get Account
     *
     *      @return string
     */
    public function Account()
    {
        return $this->Account;
    }

    /**
     *      Get OrderId
     *
     *      @return string
     */
    public function OrderId()
    {
        return $this->OrderId;
    }

    /**
     *      Get Amount
     *
     *      @return string
     */
    public function Amount()
    {
        return $this->Amount;
    }

    /**
     *      Parse xml-request, which was previously "extracted" from the body of the http request
     *
     */
    protected function parse_request_data()
    {
        parent::parse_request_data();

        $r = $this->raw_request->get_nodes_from_request('Payment');

        foreach ($r[0]->childNodes as $child)
        {
            $this->check_and_parse_request_node($child, 'Account');
            $this->check_and_parse_request_node($child, 'OrderId');
            $this->check_and_parse_request_node($child, 'Amount');
        }
    }

    /**
     *      validate Payment request
     *
     *      @param array $options
     *      @throws Exception\Structure
     */
    public function validate_request($options)
    {
        parent::validate_request($options);

        $this->validate_element('Account');
        $this->validate_element('OrderId');
        $this->validate_element('Amount');
    }
}
