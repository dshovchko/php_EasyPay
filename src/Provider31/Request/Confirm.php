<?php

/**
 *      Class for Confirm request
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPay\Provider31\Request;

use EasyPay\Log as Log;
use EasyPay\Exception;

class Confirm extends General
{
    /**
     *      @var string 'PaymentId' node
     */
    protected $PaymentId;

    /**
     *      Check constructor
     *
     *      @param string $raw Raw request data
     */
    public function __construct($raw)
    {
        parent::__construct($raw);
    }

    /**
     *      Get PaymentId
     *
     *      @return string
     */
    public function PaymentId()
    {
        return $this->PaymentId;
    }

    /**
     *      Parse xml-request, which was previously "extracted" from the body of the http request
     *
     */
    protected function parse_request_data()
    {
        parent::parse_request_data();

        $doc = new \DOMDocument();
        $doc->loadXML($this->raw_request);
        $r = $this->getNodes($doc, 'Confirm');

        foreach ($r[0]->childNodes as $child)
        {
            $this->check_and_parse_request_node($child, 'PaymentId');
        }
    }

    /**
     *      validate Confirm request
     *
     *      @param array $options
     *      @throws Exception\Structure
     */
    public function validate_request($options)
    {
        parent::validate_request($options);

        $this->validate_element('PaymentId');
    }
}
