<?php

namespace Edev\App\Logger;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * This is a static facade wrap to the PSR-3 compliant monolog/monolog
 * package. This creates a LogLevel object based on the level
 */
class Log
{
    private $logger;

    protected function __construct()
    {
    }
    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function debug(string $message, $context = [])
    {
        return (new static)->_log($message, $context, 'debug');
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function info(string $message, $context = [])
    {
        return (new static)->_log($message, $context, 'info');
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function notice(string $message, $context = [])
    {
        return (new static)->_log($message, $context, 'notice');
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function warning(string $message, $context = [])
    {
        return (new static)->_log($message, $context, 'warning');
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function error(string $message, $context = [])
    {
        return (new static)->_log($message, $context, 'error');
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function critical(string $message, $context = [])
    {
        return (new static)->_log($message, $context, 'critical');
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function alert(string $message, $context = [])
    {
        return (new static)->_log($message, $context, 'alert');
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public static function emergency(string $message, $context = [])
    {
        return (new static)->_log($message, $context, 'emergency');
    }

    public static function log($level, $message, $context)
    {
        return (new static )->_log($message, $context, $level);
    }

    private function _log(string $message, $context = [], $level)
    {
        return $this->_loggerLevel($level)->log($message, $context);
    }

    private function _loggerLevel($level)
    {
        $class = '\Edev\App\Logger\Levels\\' . ucfirst(strtolower($level));
        return new $class();
    }
}