<?php

/**
 *      Class for raw request
 *
 *      @package php_EasyPay
 *      @version 1.1
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPay\Provider31\Request;

use EasyPay\Log as Log;
use EasyPay\Exception;

class RAW
{
    /**
     *      @var string raw request
     */
    protected $raw_request;

    /**
     *      RAW constructor
     */
    public function __construct()
    {
        $this->get_http_raw_post_data();
        $this->check_presence_request();
    }

    /**
     *      Get request string content
     *
     *      @return string
     */
    public function str()
    {
        return strval($this->raw_request);
    }

    /**
     *      Get data from the body of the http request
     *
     *      - with the appropriate configuration of php.ini they can be found
     *        in the global variable $HTTP_RAW_POST_DATA
     *
     *      - but it's easier just to read the data from the php://input stream,
     *        which does not depend on the php.ini directives and allows you to read
     *        raw data from the request body
     */
    protected function get_http_raw_post_data()
    {
        Log::instance()->add('request from ' . $_SERVER['REMOTE_ADDR']);

        $this->raw_request = file_get_contents('php://input');

        Log::instance()->debug('request received: ');
        Log::instance()->debug($this->raw_request);
        Log::instance()->debug(' ');
    }

    /**
     *      Check if presence request
     *
     *      @throws Exception\Structure
     */
    protected function check_presence_request()
    {
        if (empty($this->raw_request))
        {
            throw new Exception\Structure('An empty xml request', -50);
        }
    }

    /**
     *      Get group of nodes from XML-request
     *
     *      @param string $name
     *      @return array
     */
    public function get_nodes_from_request($name)
    {
        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        if ( ! $doc->loadXML($this->raw_request))
        {
            foreach(libxml_get_errors() as $e){
                Log::instance()->error($e->message);
            }
            throw new Exception\Structure('The wrong XML is received', -51);
        }

        return $this->getNodes($doc, $name);
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
