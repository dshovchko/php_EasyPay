<?php

namespace EasyPay;

class OpenSSL
{
    public function __construct() {}

    /**
     *      verify signature of xml
     *
     *      @param string $xml
     *      @param string $bin_sign
     *      @param resource $pub_key
     *
     *      @return integer result of checking
     */
    public function verify($xml, $bin_sign, $pub_key)
    {
        return @openssl_verify($xml, $bin_sign, $pub_key);
    }

    /**
     *      generate signature of xml
     *
     *      @param string $xml
     *      @param string $sign
     *      @param resource $priv_key
     *
     *      @return bool result of signing
     */
    public function sign($xml, &$sign, $priv_key)
    {
        return @openssl_sign($xml, $sign, $priv_key);
    }

    /**
     *      get public key
     *
     *      @param mixed $certificate
     *      @return resource
     */
    public function get_pub_key($certificate)
    {
        $pub_key = @openssl_pkey_get_public($certificate);
        $this->is_key($pub_key);

        return $pub_key;
    }

    /**
     *      get private key
     *
     *      @param mixed $certificate
     *      @return resource
     */
    public function get_priv_key($certificate)
    {
        $priv_key = @openssl_pkey_get_private($certificate);
        $this->is_key($priv_key);

        return $priv_key;
    }

    /**
     *      check key
     *
     *      @param resource $key
     *      @throws Exception\Runtime
     */
    public function is_key($key)
    {
        if ($key === FALSE)
        {
            throw new Exception\Runtime('Can not extract key from certificate!', -97);
        }
    }

}
