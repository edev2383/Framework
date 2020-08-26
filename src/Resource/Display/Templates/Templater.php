<?php

namespace Edev\Resource\Display\Template;

use Edev\System\Helpers\Arr;

class Templater
{
    public static function parse($string, $data)
    {
        //
        return self::replaceTemplates($string, $data);
    }

    /**
     *
     * @param string $string
     * @param array $data
     *
     * @return string
     */
    private static function replaceTemplates($string, $data)
    {
        $injectData = '';

        // clone original string
        $strClone = $string;

        // match all template elements
        preg_match_all('/{(.*?)}/', $string, $matches);

        $templateMatches = $matches[0];
        $variableMatches = $matches[1];

        foreach ($templateMatches as $key => $var) {

            $setIfNull = $var;
            // raw value inside the brackets
            $dataKey = $variableMatches[$key];

            if (self::_checkForCompoundValue($dataKey)) {
                $injectData = self::_returnCompoundValue($dataKey, $data);
            } else {
                // simple value {variable}
                if (self::_checkForDefaultValue($dataKey)) {
                    $setIfNull = self::_returnDefaultValue($dataKey);
                    $dataKey = self::_returnTrimmedDataKey($dataKey);
                }

                $injectData = isset($data[$dataKey]) ? $data[$dataKey] : $setIfNull;

                // echo "<p>[ value injected: $injectData ] [ setIfNull: $setIfNull ]</p>";
            }

            // replace the {variable} $var, with any found data
            $strClone = str_replace($var, $injectData, $strClone);
        }

        return $strClone;
    }

    private static function _returnTrimmedDataKey($string)
    {
        return trim(explode('||', $string)[0]);
    }

    /**
     *
     * @param string $string
     * @param array $data
     *
     * @return string
     */
    private static function _returnCompoundValue($string, $data)
    {
        // break the incoming string to array and prop names
        $varArr = explode(':', $string);
        $arrayName = $varArr[0];
        $propName = $varArr[1];

        // check if propname has a default value set
        $hasDefault = self::_checkForDefaultValue($propName);

        // if there is a default value, trim it from the propName value
        if ($hasDefault) {
            $propName = trim(explode('||', $propName)[0]);
        }

        // get the retrn value from the data array
        $returnValue = $data[$arrayName][$propName];
        // $returnValue = $data[$arrayName];

        // if returnValue comes back null, check for a default value, if not, return the original string
        if (is_null($returnValue)) {
            if ($hasDefault) {
                return self::_returnDefaultValue($string); // return the stated default
            }
            return '{ ' . $string . '}'; // return original string
        }
        return $returnValue; // return the found value
    }

    /**
     *
     * @param string $string
     *
     * @return boolean
     */
    private static function _returnDefaultValue($string)
    {
        return trim(explode('||', $string)[1]);
    }

    /**
     *
     * @param string $string
     *
     * @return boolean
     */
    private static function _checkForCompoundValue($string)
    {
        return strpos($string, ':') != false;
    }

    /**
     *
     * @param string $string
     *
     * @return boolean
     */
    private static function _checkForDefaultValue($string)
    {
        return strpos($string, '||') != false;
    }
}