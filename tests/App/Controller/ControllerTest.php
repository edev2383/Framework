<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Edev\System\Helpers\Arr;

final class ControllerTest extends TestCase
{
    public function testCanSetPropsObjectInController(): void
    {

        $props = ['foo' => 'bar']; 
        $c = new \Edev\Controller\Controller();
        $c->setProps($props);
        $this->assertEquals('bar', $c->props->foo);
    }
    
    public function testControllerCreatesMetaPdoConnection(): void
    {
        $c = new \Edev\Controller\Controller();
        $c->connect();
        $this->assertNotEmpty(\Edev\Database\Container::getInstance()->getConnectionByName('meta'));
    }

    public function testControllerCreatesClientPDOConnection(): void
    {
        $c = new \Edev\Controller\Controller();
        $c->connect();
        $x = \Edev\Database\Container::getInstance()->getConnectionByName('client');
        $this->assertNotEmpty(\Edev\Database\Container::getInstance()->getConnectionByName('client'));

    }
}