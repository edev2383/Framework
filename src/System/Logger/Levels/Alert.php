<?php

namespace Edev\App\Logger\Levels;

use Edev\App\Logger\LogLevel;

class Alert extends LogLevel
{
    protected $level = 'alert';
    protected function _additionalActions(string $string, $data = [])
    {
    }
}