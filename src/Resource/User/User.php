<?php

namespace Edev\Resource\User;

use Edev\Model\Employee;

class User
{
    private static $instance;
    private $userId;
    private $loggedIn;
    private $status;
    private $email;
    private $firstName;
    private $lastName;

    private function __construct($baseUserSession = [])
    {
    }

    public static function getInstance($baseUserSession = [])
    {
        if (!isset(self::$instance)) {
            self::$instance = new \Edev\Resource\User\User($baseUserSession);
        }

        return self::$instance;
    }

    private function _setUser($baseUserSession)
    {  
    }

    public function isLoggedIn() {
        return false;
    }
}
