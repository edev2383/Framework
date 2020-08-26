<?php

namespace Edev\Resource;

class Crypto
{

    /**
     * key values:
     *      PRIVATE_KEY: use to pass values from controller to contorller
     *      ENCRYPT_META_DATA_KEY: encrypt dbu_user & password to client_meta table
     */
    public function __construct()
    {
    }

    public static function encrypt($string, $key)
    {

        // get key and method from .env
        $_key = \Edev\Resource\DotEnv::get($key);
        $_method = \Edev\Resource\DotEnv::get('CIPHER_METHOD');

        // define nonceSize and nonce value
        $nonceSize = openssl_cipher_iv_length($_method);
        $nonce = openssl_random_pseudo_bytes($nonceSize);

        // encrypt the supplied string
        $cipherText = openssl_encrypt($string, $_method, $_key, OPENSSL_RAW_DATA, $nonce);

        // return the encrypted string with the nonce prefix attached and base64 encode it
        return base64_encode($nonce . $cipherText);
    }

    public static function decrypt($cipher, $key)
    {

        // get key and method from .env
        $_key = \Edev\Resource\DotEnv::get($key);
        $_method = \Edev\Resource\DotEnv::get('CIPHER_METHOD');

        // decode from base64
        $message = base64_decode($cipher, true);

        // define noncesize from _method
        $nonceSize = openssl_cipher_iv_length($_method);

        // break the nonce and ciphertext values
        $nonce = mb_substr($message, 0, $nonceSize, '8bit');
        $ciphertext = mb_substr($message, $nonceSize, null, '8bit');

        // decrypt
        $plainText = openssl_decrypt($ciphertext, $_method, $_key, OPENSSL_RAW_DATA, $nonce);

        // return
        return $plainText;
    }
}