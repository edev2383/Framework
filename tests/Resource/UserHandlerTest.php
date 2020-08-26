<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Edev\System\Helpers\Arr;
use Edev\System\Helpers\Domain;

final class UserHandlerTest extends TestCase
{
    public function testInvalidEmailReturnsException(): void
    {

        //
        $invalidEmail = 'jfdklsATjet.com';
        $user = new \Edev\Resource\UserHandler();
        $rH = new \Edev\Resource\RegexHandler();
        $this->expectException(\Exception\RegexEmail::class);
        $user->verifyEmail($rH, $invalidEmail);
    }

    public function testValidEmailReturnsTrue(): void
    {
        $validEmail = 'thisIsAValidEmail@Email.com';
        $user = new \Edev\Resource\UserHandler();
        $rH = new \Edev\Resource\RegexHandler();
        $this->assertTrue($user->verifyEmail($rH, $validEmail));
    }

    public function testInvalidPinReturnsException(): void
    {
        //
        $invalidPin = '555a';
        $user = new \Edev\Resource\UserHandler();
        $rH = new \Edev\Resource\RegexHandler();
        $this->expectException(\Exception\RegexPin::class);
        $user->verifyPin($rH, $invalidPin);
    }

    public function testValidPinReturnsTrue(): void
    {
        $validPin = '5555';
        $user = new \Edev\Resource\UserHandler();
        $rH = new \Edev\Resource\RegexHandler();
        $this->assertTrue($user->verifyPin($rH, $validPin));
    }
}