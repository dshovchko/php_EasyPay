<?php

namespace EasyPay;

use EasyPay\Log as Log;
use EasyPay\Exception;
use EasyPay\Key as Key;
use EasyPay\OpenSSL as OpenSSL;

class Sign
{
    public function __construct() {}

    /**
     *      Verify signature of request
     *
     *      @param array $options
     */
    public function verify($request_str, $sign, $options)
    {
        if (isset($options['UseSign']) && ($options['UseSign'] === true))
        {
            $this->check_verify_sign_result(
                $result = (new OpenSSL())->verify(
                    str_replace($sign, '', $request_str),
                    pack("H*", $sign),
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
}
