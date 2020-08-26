<?php

namespace Edev\Database\Manager;

class Conn
{

    protected $Manager;

    public function __construct()
    {
        $this->_init();
    }

    private function _init()
    {
        $this->_registerManager(new Manager());
        $this->_createConnection();
    }

    private function _registerManager(Manager $manager)
    {
        $this->Manager = $manager;
    }

    protected function _createConnection()
    {
        $this->Manager->createConnection();
    }
}