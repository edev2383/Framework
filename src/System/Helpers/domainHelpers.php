<?php

namespace Edev\System\Helpers;

class Domain
{

    private static $_devWhiteList = ['dev'];

    public static function get()
    {
        return explode('.', $_SERVER['SERVER_NAME'])[0];
    }

    public static function isDev()
    {
        return in_array(Domain::get(), self::$_devWhiteList);
    }
}