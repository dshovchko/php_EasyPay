<?php

/**
 *      General class for all request types
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPay\Provider31\Request;

use EasyPay\Log as Log;
use EasyPay\Exception;
use EasyPay\Key as Key;
use EasyPay\OpenSSL as OpenSSL;

class General
{
    /**
     *      @var string raw request
     */
    protected $raw_request;

    /**
     *      @var string 'DateTime' node
     */
    protected $DateTime;

    /**
     *      @var string 'Sign' node
     */
    protected $Sign;

    /**
     *      @var string 'Operation' type
     */
    protected $Operation;

    /**
     *      @var string 'ServiceId' node
     */
    protected $ServiceId;

    /**
     *      @var array list of possible operations
     */
    protected $operations = array('Check','Payment','Confirm','Cancel');

    /**
     *      General constructor
     *
     *      @param string $raw Raw request data
     */
    public function __construct($raw)
    {
        $this->raw_request = strval($raw);

        $this->parse_request_data();
    }

    /**
     *      Get DateTime
     *
     *      @return string
     */
    public function DateTime()
    {
        return $this->DateTime;
    }

    /**
     *      Get Sign
     *
     *      @return string
     */
    public function Sign()
    {
        return $this->Sign;
    }

    /**
     *      Get Operation type
     *
     *      @return string
     */
    public function Operation()
    {
        return $this->Operation;
    }

    /**
     *      Get ServiceId
     *
     *      @return string
     */
    public function ServiceId()
    {
        return $this->ServiceId;
    }

    /**
     *      Parse xml-request, which was previously "extracted" from the body of the http request
     *
     *      @throws Exception\Structure
     */
    protected function parse_request_data()
    {
        if (empty($this->raw_request))
        {
            throw new Exception\Structure('An empty xml request', -50);
        }

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        if ( ! $doc->loadXML($this->raw_request))
        {
            foreach(libxml_get_errors() as $e){
                Log::instance()->error($e->message);
            }
            throw new Exception\Structure('The wrong XML is received', -51);
        }

        // process <Request> group
        $r = $this->getNodes($doc, 'Request');

        if (count($r) < 1)
        {
            throw new Exception\Structure('The xml-query does not contain any element Request!', -52);
        }

        foreach ($r[0]->childNodes as $child)
        {
            if ($child->nodeName == 'DateTime')
            {
                $this->parse_request_node($child, 'DateTime');
            }
            elseif ($child->nodeName == 'Sign')
            {
                $this->parse_request_node($child, 'Sign');
            }
            elseif (in_array($child->nodeName, $this->operations))
            {
                if ( ! isset($this->Operation))
                {
                    $this->Operation = $child->nodeName;
                }
                else
                {
                    throw new Exception\Structure('There is more than one Operation type element in the xml-query!', -53);
                }
            }
        }

        if ( ! isset($this->Operation))
        {
            throw new Exception\Structure('There is no Operation type element in the xml request!', -55);
        }

        // process <Operation> group
        $r = $this->getNodes($doc, $this->Operation);

        foreach ($r[0]->childNodes as $child)
        {
            if ($child->nodeName == 'ServiceId')
            {
                $this->parse_request_node($child, 'ServiceId');
            }
        }
    }

    /**
     *      Parse node of request
     *
     *      @param \DOMNode $n
     *      @param string $name
     *
     *      @throws Exception\Structure
     */
    protected function parse_request_node($n, $name)
    {
        if ( ! isset($this->$name))
        {
            $this->$name = $n->nodeValue;
        }
        else
        {
            throw new Exception\Structure('There is more than one '.$name.' element in the xml-query!', -56);
        }
    }

    /**
     *      "Rough" validation of the received xml request
     *
     *      @param array $options
     *      @throws Exception\Data
     *      @throws Exception\Structure
     */
    public function validate_request($options)
    {
        $this->validate_element('DateTime');
        $this->validate_element('Sign');
        $this->validate_element('ServiceId');

        // compare received value ServiceId with option ServiceId
        if (intval($options['ServiceId']) != intval($this->ServiceId))
        {
            throw new Exception\Data('This request is not for our ServiceId!', -58);
        }
    }

    /**
     *      Validation of xml-element
     *
     *      @param string $name
     */
    public function validate_element($name)
    {
        if ( ! isset($this->$name))
        {
            throw new Exception\Structure('There is no '.$name.' element in the xml request!', -57);
        }
    }

    /**
     *      Verify signature of request
     *
     *      @param array $options
     */
    public function verify_sign($options)
    {
        if (isset($options['UseSign']) && ($options['UseSign'] === true))
        {
            $this->check_verify_sign_result(
                $result = (new OpenSSL())->verify(
                    str_replace($this->Sign, '', $this->raw_request),
                    pack("H*", $this->Sign),
                    (new OpenSSL())->get_pub_key($this->get_pub_key($options))
                )
            );
        }
    }

    /**
     *      load file with easysoft public key
     *
     *      @param array $options
     *      @throws Exception\Runtime
     *      @return string
     */
    protected function get_pub_key($options)
    {
        if ( ! isset($options['EasySoftPKey']))
        {
            throw new Exception\Runtime('The parameter EasySoftPKey is not set!', -94);
        }

        return (new Key())->get($options['EasySoftPKey'], 'public');
    }

    /**
     *      check result of openssl verify signature
     *
     *      @param integer $result
     *      @throws Exception\Sign
     */
    protected function check_verify_sign_result($result)
    {
        if ($result == -1)
        {
            throw new Exception\Sign('Error verify signature of request!', -96);
        }
        elseif ($result == 0)
        {
            throw new Exception\Sign('Signature of request is incorrect!', -95);
        }
    }

    /**
     *      Selects nodes by name
     *
     *      @param \DOMDocument $dom
     *      @param string $name
     *      @param array $ret
     *
     *      @return array nodes with the name
     */
    protected function getNodes($dom, $name, $ret=array())
    {
        foreach($dom->childNodes as $child)
        {
            if ($child->nodeName == $name)
            {
                array_push($ret, $child);
            }
            else
            {
                if (count($child->childNodes) > 0)
                {
                    $ret = $this->getNodes($child, $name, $ret);
                }
            }
        }

        return $ret;
    }

}
