<?php

namespace Tests;

use App\Controllers\StockController;
use App\Services\StockService;
use PHPUnit\Framework\TestCase;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

class StockEndpointTest extends BaseTestCase
{
    private $stockServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->stockServiceMock = $this->createMock(StockService::class);
        $this->stockServiceMock->method('getStockData')
            ->willReturn([
                "symbol" => "AAPL.US",
                "date" => "2024-08-26",
                "time" => "22:00:18",
                "open" => 226.76,
                "high" => 227.28,
                "low" => 223.8905,
                "close" => 227.22,
                "volume" => 30526832,
                "name" => "APPLE"
            ]);

        $this->app->getContainer()->set(StockController::class, function () {
            return new StockController($this->stockServiceMock);
        });
    }

    public function testStockEndpointReturnsCorrectData()
    {
        $response = $this->request('GET', '/stock', ['q' => 'aapl.us']);

        $this->assertEquals(200, $response->getStatusCode());

        $expectedPayload = json_encode([
            "name"   => "APPLE",
            "symbol" => "AAPL.US",
            "open"   => 226.76,
            "high"   => 227.28,
            "low"    => 223.8905,
            "close"  => 227.22
        ]);

        $this->assertJsonStringEqualsJsonString($expectedPayload, (string) $response->getBody());
    }

    public function testStockEndpointRequiresQueryParam()
    {
        $response = $this->request('GET', '/stock');

        $this->assertEquals(400, $response->getStatusCode());

        $expectedError = json_encode(["error" => "Stock code is required."]);
        $this->assertJsonStringEqualsJsonString($expectedError, (string) $response->getBody());
    }
}
