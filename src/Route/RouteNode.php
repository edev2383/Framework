<?php

namespace Edev\Route;

class RouteNode
{

    private $_method;

    public function __construct(string $method)
    {
        $this->_method = $method;
    }

    public function getMethod()
    {

        return $this->_method;
    }

    public function overrideMethod($method)
    {
        $this->_method = $method;
    }
}
