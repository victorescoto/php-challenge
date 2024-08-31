<?php

namespace App\Services;

use GuzzleHttp\Client;

class StockService
{
    private Client $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client([
            'base_uri' => 'https://stooq.com/',
            'timeout'  => 5.0,
        ]);
    }

    private function makeRequest(string $stockCode): ?array
    {
        $response = $this->httpClient->get('/q/l/', [
            'query' => [
                's' => $stockCode,
                'f' => 'sd2t2ohlcvn',
                'h' => '',
                'e' => 'json' // instead of 'csv'
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true)['symbols'][0];
    }

    public function getStockData(string $stockCode): ?array
    {
        $data = $this->makeRequest($stockCode);

        if (!isset($data['date'])) {
            return null;
        }

        return $data;
    }
}
