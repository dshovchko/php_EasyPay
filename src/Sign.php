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
     *      @param string $request_str
     *      @param string $sign
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

    /**
     *      Generate signature of response
     *
     *      @param string $request_str
     *      @param array $options
     *      @return string
     */
    public function generate($request_str, $options)
    {
        try
        {
            $sign = '';
            $this->check_generate_sign_result(
                $result = (new OpenSSL())->sign(
                    $request_str,
                    $sign,
                    (new OpenSSL())->get_priv_key($this->get_priv_key($options))
                )
            );

            return strtoupper(bin2hex($sign));
        }
        catch (\Exception $e)
        {
            return null;
        }
    }

    /**
     *      load file with provider private key
     *
     *      @param array $options
     *      @throws Exception\Runtime
     *      @return string
     */
    protected function get_priv_key($options)
    {
        if ( ! isset($options['ProviderPKey']))
        {
            throw new \EasyPay\Exception\Runtime('The parameter ProviderPKey is not set!', -94);
        }

        return (new Key())->get($options['ProviderPKey'], 'private');
    }

    /**
     *      check result of openssl sign
     *
     *      @param bool $result
     *      @throws Exception\Sign
     */
    protected function check_generate_sign_result($result)
    {
        if ($result === FALSE)
        {
            throw new \EasyPay\Exception\Sign('Can not generate signature!', -96);
        }
    }
}
