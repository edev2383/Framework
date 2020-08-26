<?php

namespace Edev\Database\Manager;

use Edev\System\Helpers\Arr;

class Manager
{

    public function createConnection()
    {
        $this->_createConnection();
    }

    public function createClientConnection()
    {
        $this->_createClientConnection();
    }

    public function reconnectClient()
    {
        $this->_createClientConnection();
    }

    private function _createConnection()
    {
        $config = [
            'host' => \Edev\Resource\DotEnv::get('DB_HOST'),
            'database' => \Edev\Resource\DotEnv::get('DB_NAME'),
            'username' => \Edev\Resource\DotEnv::get('DB_USER'), //KQO93827DKFOLFHGIDLS
            'password' => \Edev\Resource\DotEnv::get('DB_PASS'),
            'name' => 'default',
        ];

        $cf = new \Edev\Database\Connector\ConnectionFactory(\Edev\Database\Container::getInstance());
        $cf->make($config);
    }
}