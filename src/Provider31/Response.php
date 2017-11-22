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
use EasyPay\Key as Key;

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
        //$this->save('/tmp/test1.xml');

        return $this->saveXML(NULL, LIBXML_NOEMPTYTAG);
    }

    /**
     *      Send response
     *
     *      @param array $options
     */
    public function out($options)
    {
        $this->sign($options);

        Log::instance()->debug('response sends: ');
        Log::instance()->debug($this->friendly());

        ob_clean();
        header("Content-Type: text/xml; charset=utf-8");
        echo $this->friendly();
        exit;
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

            $sign = $this->generate_sign($options);

            $this->Sign->nodeValue = $sign;
        }
    }

    /**
     *      Generate signature of response
     *
     *      @param array $options
     *      @return string
     */
    public function generate_sign($options)
    {
        if ( ! isset($options['ProviderPKey']))
        {
            Log::instance()->error('The parameter ProviderPKey is not set!');
            return null;
        }
        try
        {
            $pkeyid = (new Key())->get($options['ProviderPKey'], 'private');
        }
        catch (\Exception $e)
        {
            return null;
        }

        $pr_key = openssl_pkey_get_private($pkeyid);
        if ($pr_key === FALSE)
        {
            Log::instance()->error('Can not extract the private key from certificate!');
            return null;
        }
        if (openssl_sign($this->friendly(), $sign, $pr_key) === FALSE)
        {
            Log::instance()->error('Can not generate signature!');
            return null;
        }

        return strtoupper(bin2hex($sign));
    }
}
