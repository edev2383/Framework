<?php

namespace Edev\App\Logger\Levels;

use Edev\App\Logger\LogLevel;

class Emergency extends LogLevel
{
    protected $level = 'emergency';
    protected function _additionalActions(string $string, $data = [])
    {
    }
}