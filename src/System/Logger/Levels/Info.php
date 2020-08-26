<?php

namespace Edev\App\Logger\Levels;

use Edev\App\Logger\LogLevel;

class Info extends LogLevel
{
    protected $level = 'info';
    protected function _additionalActions(string $string, $data = [])
    {
    }
}