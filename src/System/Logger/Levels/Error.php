<?php

namespace Edev\App\Logger\Levels;

use Edev\App\Logger\LogLevel;

class Error extends LogLevel
{
    protected $level = 'error';
    protected function _additionalActions(string $string, $data = [])
    {
    }
}