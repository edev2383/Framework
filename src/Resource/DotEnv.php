<?php

namespace Edev\Resource;

class DotEnv
{

    public function __construct()
    {
        //
    }

    public static function get($varName)
    {

        try {

            // check for value type
            if (preg_match('/[a-z0-9=!@#$%^&*()]/', $varName)) {
                throw new \Exception\IllegalValueException('dotEnv keys must be all UPPERCASE letters. No numbers or symbols');
            }

            // pad the end with an = sign, we want to fully match the keys. Could use a regex here, most likely, but this will work for v1.
            $varName .= '=';

            // debug code allowing for switches between dev/production server
            $reqURI = $_SERVER['SERVER_NAME'];
            $reqRoot = explode('.', $reqURI)[0];

            // set file path
            $file = '../.env';

            // set open handler
            $handle = fopen($file, 'r');

            // if handle exists...
            if ($handle) {

                // loop through each line
                while (($line = fgets($handle)) !== false) {

                    // if the beginning of the line, for the length of the key (+1) matches the padded $varName...
                    if (substr($line, 0, strlen($varName)) == $varName) {
                        // break the line at the = and return the value
                        $val = trim(explode('=', $line)[1]);
                        fclose($handle); // close the handle
                        return $val; // return the value
                    }
                }
                // throw key not found
                // throw new \Exception\IllegalValueException('dotEnv key not found.');
            } else {
                // error opening the file.
                throw new \Exception\IllegalValueException('dotEnv File not found.');
            }

            // return false as default
            return null;
        } catch (\Exception\IllegalValueException $e) {
            die($e->getMessage());
        }
    }
}
