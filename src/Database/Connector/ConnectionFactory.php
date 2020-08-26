<?php

namespace Edev\Database\Connector;

use Edev\Database\Container as PdoContainer;

class ConnectionFactory
{

    public function __construct(PdoContainer $container)
    {
        $this->container = $container;
    }

    public function make(array $config)
    {
        $name = $this->getName($config);
        $connection = $this->createConnection($config);
        $this->container->addConnection($name, $connection);
    }

    public function getName($config)
    {
        return $config['name'] ?? 'default';
    }

    protected function createConnection(array $config)
    {
        $connector = $this->createConnector();
        return $connector->connect($config);
    }
    public function createConnector()
    {
        return new Connector;
    }
}