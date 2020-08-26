<?php

namespace Edev\App\Logger\Levels;

use Edev\App\Logger\LogLevel;

class Critical extends LogLevel
{
    protected $level = 'critical';
    protected function _additionalActions(string $string, $data = [])
    {
    }
}