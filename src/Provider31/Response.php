<?php

/**
 *      Abstract class for the family of response classes
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPay\Provider31;

use EasyPay\Log as Log;
use EasyPay\Sign as Sign;

abstract class Response extends \DomDocument
{
    /**
     *      @var string
     *
     */
    const TEMPLATE = '<Response><StatusCode></StatusCode><StatusDetail></StatusDetail><DateTime></DateTime></Response>';

    /**
     *      @var \DOMNode
     */
    protected $Response;

    /**
     *      @var \DOMElement
     */
    protected $Sign;

    /**
     *      Response constructor
     *
     */
    public function __construct()
    {
        parent::__construct('1.0', 'UTF-8');

        $this->loadXML(self::TEMPLATE);

        $this->Response = $this->firstChild;
        $this->set_DateTime();
    }

    /**
     *      Set DateTime element value by current time
     */
    public function set_DateTime()
    {
        $this->setElementValue('DateTime', date('Y-m-d\TH:i:s', time()));
    }

    /**
     *      Create new element node
     *
     *      @param string $name
     *      @param string $value (optional)
     */
    public function createElement($name, $value=NULL)
    {
        return parent::createElement($name, $value);
    }

    /**
     *      Set node value
     *
     *      @param string $name
     *      @param string $value
     */
    public function setElementValue($name, $value)
    {
        foreach ($this->Response->childNodes as $child)
        {
            if ($child->nodeName == $name)
            {
                $child->nodeValue = $value;
            }
        }
    }

    /**
     *      Dumps response into a string
     *
     *      @return string XML
     */
    public function friendly()
    {
        $this->encoding = 'UTF-8';
        $this->formatOutput = true;

        return $this->saveXML(NULL, LIBXML_NOEMPTYTAG);
    }

    /**
     *      Sign and send response
     *
     *      @param array $options
     */
    public function sign_and_out($options)
    {
        $this->sign($options);
        $this->out_header();
        $this->out_body($this->friendly());
    }

    /**
     *      Send header
     */
    protected function out_header()
    {
        ob_clean();
        @header("Content-Type: text/xml; charset=utf-8");
    }

    /**
     *      Send body
     *
     *      @param string $body
     */
    protected function out_body($body)
    {
        Log::instance()->debug('response sends: ');
        Log::instance()->debug($body);

        echo $body;
    }

    /**
     *      Add Sign (if hasn't yet done)
     *
     *      @param array $options
     */
    protected function sign($options)
    {
        if (isset($this->Sign)) return;

        if (isset($options['UseSign']) && ($options['UseSign'] === true))
        {
            $this->Sign = $this->createElement('Sign');
            $this->Response->appendChild($this->Sign);

            $this->Sign->nodeValue = (new Sign())->generate($this->friendly(), $options);
        }
    }
}
