<?php

namespace Edev\Resource;

use Edev\System\Helpers\Arr;
use Edev\System\Helpers\Str;

class Props
{
    public function __construct($props)
    {
        $this->_init($props);
    }

    /**
     * Set each incoming value to a top-level property on the Props
     * object, accessed in controllers as $this->props
     *
     * @param [type] $props
     * @return void
     */
    private function _init($props)
    {
        foreach ($props as $key => $val) {
            if ($key != 'uri') {
                if (Str::isJSON($val)) {
                    $val = json_decode($val, true);
                }
                $this->{$key} = $this->_sanitize($val);
            }
        }
    }

    /**
     * Run all values to sanitation process
     *
     * @param [type] $value
     * @return mixed
     */
    private function _sanitize($value)
    {
        if (is_array($value) || is_object($value)) {
            return $this->_arraySanitize((array) $value);
        }
        return htmlentities($value);
    }

    /**
     * Cycle through an array and sanitize each value
     *
     * @param array $array
     * @return void
     */
    private function _arraySanitize(array $array)
    {
        $c = [];
        foreach ($array as $k => $v) {
            $c[$k] = $this->_sanitize($v);
        }
        return $c;
    }
}
