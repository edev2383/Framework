<?php

namespace Edev\App\Logger\Levels;

use Edev\App\Logger\LogLevel;

class Debug extends LogLevel
{
    protected $level = 'debug';
    protected function _additionalActions(string $string, $data = [])
    {
    }
}