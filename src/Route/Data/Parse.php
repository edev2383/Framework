<?php

namespace Edev\Route\Data;

use Edev\System\Helpers\Str;

class Parse
{

    private $data;
    private $request_method;
    private $re = '/^=%7B.*%7D$/';

    public static function parse($request_method, $data)
    {
        return (new static)->_parse($request_method, $data);
    }

    private function _parse($request_method, $data)
    {
        if (!is_null($data)) {
            return $this->_formatData($data);
        }
        return;
    }

    private function _formatData($data)
    {
        // if JSON is retrieved, return decoded JSON
        if (Str::isJSON($data)) {
            return (array) json_decode($data);
        } else {
            // parse the string, if string is empty, then we have encoded JSON
            parse_str($data, $capture);
            if (empty($capture)) {
                return $this->_deepFormat($data);
            } else {
                // return array capture
                return $capture;
            }
        }
    }

    private function _deepFormat($data)
    {
        if (Str::isJSON($data)) {
            return (array) json_decode($data);
        } else if ($this->_isRawURLEncodedJSON($data)) {
            return (array) $this->_attackAndDethroneGod($data);
        } else {
            return [];
        }
    }

    private function _attackAndDethroneGod($data)
    {
        return json_decode(urldecode($this->_trim($data)));
    }

    private function _isRawURLEncodedJSON($data)
    {
        return preg_match($this->re, $data);
    }

    private function _trim($data)
    {
        return str_replace('=', '', $data);
    }
}