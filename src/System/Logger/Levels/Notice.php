<?php

namespace Edev\App\Logger\Levels;

use Edev\App\Logger\LogLevel;

class Notice extends LogLevel
{
    protected $level = 'notice';
    protected function _additionalActions(string $string, $data = [])
    {
    }
}