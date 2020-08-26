<?php

namespace Edev\System\Helpers;

class Arr
{
    public static function retMulti($array, $col = 'id')
    {
        if (is_null($array)) {
            return null;
        }
        return empty(array_count_values(array_column($array, $col))) ? [$array] : $array;
    }

    public static function flatten($array = [])
    {
        // short circuit if there is no value
        if (is_null($array)) {
            return null;
        }

        // if not array, make a single val array and return it
        if (!is_array($array)) {
            return [$array];
        }

        return self::returnFlatArray($array);
    }

    private static function returnFlatArray($array)
    {
        $c = [];
        foreach ($array as $val) {
            $c[] = current($val);
        }
        return $c;
    }

    public static function pre($array)
    {
        echo '<pre>';
        if (isset($_GET['locate'])) {
            print_r($_SERVER);
        }
        print_r($array);
        echo '</pre>';
    }
}