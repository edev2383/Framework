<?php

namespace Edev\App\Logger\Levels;

use Edev\App\Logger\LogLevel;

class Warning extends LogLevel
{
    protected $level = 'warning';
    protected function _additionalActions(string $string, $data = [])
    {
    }
}