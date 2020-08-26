<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Edev\System\Helpers\Arr;

final class AttendanceControllerTest extends TestCase
{

    protected $_isController = true;
    

    public function setUp():void {

        $this->http = new \GuzzleHttp\Client(['base_uri' => 'http://dev.zerodock.com/']);
    }
    
    public function tearDown():void {
        $this->http = null;
    }

    public function testInvalidRouteReturns404(): void {
        $response = $this->http->request('GET', 'paat/invalid', ['http_errors' => false]);
    
        $this->assertEquals(404, $response->getStatusCode());

    }
    public function testRandomResourceRoutesWork(): void
    {
        // $routeController::resource('paat', 'AttendanceTrackerController', 'Paat');
        $routes = ['paat', 'paat/create', 'paat/1', 'paat/4/edit'];
        $response = $this->http->request('GET', $routes[rand(0, 3)]);
        $this->assertEquals(200, $response->getStatusCode());
    }
}