<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Edev\System\Helpers\Arr;
use Edev\System\Helpers\Domain;

final class UserControllerTest extends TestCase
{
    public function testInvalidLoginInfoReturnsZeroAsStatus(): void
    {
        $email = 'nonsense@email.com';
        $password = 'QWERTY9080';
        $user = new \Edev\Resource\UserHandler();
        $loginStatus = $user->verifyUser($email, $password, null);
        $this->assertEquals(0, $loginStatus['status']);
    }

    public function testInvalidPinReturnsZeroAsStatus(): void
    {
        $pin = '9999';
        $user = new \Edev\Resource\UserHandler();
        $loginStatus = $user->verifyUser(null, null, $pin);
        $this->assertEquals(0, $loginStatus['status']);
    }
}