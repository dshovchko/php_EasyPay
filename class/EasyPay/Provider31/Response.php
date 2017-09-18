<?php

/**
 *      Abstract class for the family of response classes
 *
 *      @package php_EasyPay
 *      @version 1.0
 *      @author Dmitry Shovchko <d.shovchko@gmail.com>
 *
 */

namespace EasyPay\Provider31;

abstract class Response extends \DomDocument
{
        /**
         *      @var string
         *
         */
        const TEMPLATE = '<Response><StatusCode></StatusCode><StatusDetail></StatusDetail><DateTime></DateTime></Response>';
        
        /**
         *      Response constructor
         *      
         */
        function __construct()
        {
                parent::__construct('1.0', 'UTF-8');

                self::loadXML(self::TEMPLATE);
                
                $this->Response = $this->firstChild;
                $this->setElementValue('DateTime', date('Y-m-d\TH:i:s', time()));
        }
        
        /**
         *      Create new element node
         *
         *      @param string $name
         *      @param string $value (optional)
         */
        function createElement($name, $value=NULL)
        {
                return parent::createElement($name, $value);
        }
        
        /**
         *      Create new node attribute
         *
         *      @param string $name
         *      @param string $value
         */
        function create_attr($name, $value)
        {
                return new DOMAttr($name, $value);
        }
        
        /**
         *      Set node value
         *
         *      @param string $name
         *      @param string $value
         */
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
        
        /**
         *      Dumps response into a string
         *
         *      @return string XML
         */
        function friendly()
        {
                $this->encoding = 'UTF-8';
                //$this->save('/tmp/test1.xml');

                return $this->saveXML(NULL, LIBXML_NOEMPTYTAG);
        }
}