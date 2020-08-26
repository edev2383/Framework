<?php

namespace Edev\App\Logger;

use Monolog\Handler\StreamHandler;
use Monolog\Logger as Logger;

class LogLevel
{

    protected $logger;
    protected $logSuffix = 'system';
    protected $level = 'debug';

    public function __construct()
    {
        $this->_init();
    }

    protected function _init()
    {
        $this->_registerLogger();
    }

    protected function _registerLogger()
    {
        $this->logger = new Logger('Edev_logs');
        $this->logger->pushHandler(new StreamHandler($this->_generateFilename(), Logger::DEBUG));
    }

    protected function _generateFilename()
    {
        $this->_generateConnection();
        return './logs/' . date('Ymd') . '-' . \Edev\Resource\Client\Client::getInstance()->getPrefix() . '-' . $this->logSuffix . '.log';
    }

    public function log(string $string, $data = [])
    {
        $this->_additionalActions($string, $data);
        return $this->_sendLog($string, $data);
    }

    protected function _additionalActions(string $string, $data = [])
    {
    }

    protected function _sendLog(string $string, $data = [])
    {
        $level = strtolower($this->level);
        $this->logger->{$level}($string, $data);
    }
    protected function _generateConnection()
    {
        if (!\Edev\Database\Container::getInstance()->connectionExists('meta')) {
            // return new \Edev\Database\Manager\Manager();
        }
    }
}