<?php

namespace EasyPay\Provider31;

abstract class Response extends \DomDocument
{
        const TEMPLATE = '<Response><StatusCode></StatusCode><StatusDetail></StatusDetail><DateTime></DateTime></Response>';
        
        function __construct()
        {
                parent::__construct('1.0', 'UTF-8');

                self::loadXML(self::TEMPLATE);
                
                $this->Response = $this->firstChild;
                $this->setElementValue('DateTime', date('Y-m-d\TH:i:s', time()));
        }
        
        function createElement($name, $value=NULL)
        {
                return parent::createElement($name, $value);
        }
        
        function create_attr($name, $value)
        {
                return new DOMAttr($name, $value);
        }
        
        function setElementValue($name, $value)
        {
                foreach ($this->Response->childNodes as $child)
                {
                        if ($child->nodeName == $name)
                        {
                                $child->nodeValue = $value;
                        }
                }
        }
        
        function friendly()
        {
                $this->encoding = 'UTF-8';
                //$this->save('/tmp/test1.xml');

                return $this->saveXML(NULL, LIBXML_NOEMPTYTAG);
        }
}