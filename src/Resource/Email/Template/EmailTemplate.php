<?php

namespace Edev\Resource;

use Exception\Exception;

class EmailTemplate
{
    private static $_path = '/home/zerodock/public_html/View/template/email/';

    public static function load($filename)
    {
        // phpCore try catch block
        try {


            $path = self::$_path . $filename . '.html';

            // code block ....
            if (!file_exists($path)) {
                throw new \Exception("Email template file [$path] does not exist.");
            }

            return file_get_contents($path);
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    // public static function isModule()
    // {
    //     $dir = getcwd();
    //     return strpos($dir, 'module') !== false;
    // }
}