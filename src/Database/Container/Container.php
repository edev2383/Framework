<?php

namespace Edev\Database;

class Container
{

    protected static $instance;

    protected $collection = [];

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    public function addConnection($name, $pdo)
    {
        $this->collection[$name] = $pdo;
    }

    public function clearConnection($name)
    {
        unset($this->collection[$name]);
    }

    public function getConnectionByName($name)
    {
        if (!array_key_exists($name, $this->collection)) {
            echo '<pre>';
            debug_print_backtrace();
            die();
        }
        return $this->collection[$name];
    }

    public function clearAll()
    {
        foreach ($this->collection as $name => $conn) {
            $this->clearConnection($name);
        }
    }
    public function connectionExists(string $name)
    {
        return isset($this->collection[$name]);
    }
}
