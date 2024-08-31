<?php

namespace App\Controllers;

use App\Entities\StockQuery;
use App\Services\MessageBrokerService;
use App\Services\StockQueryService;
use App\Services\StockService;
use PhpAmqpLib\Exception\AMQPIOException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class StockController extends BaseController
{
    public function __construct(
        private StockService $stockService,
        private StockQueryService $stockQueryService,
        private MessageBrokerService $messageBrokerService
    ) {}

    public function stock(Request $request, Response $response, $args): Response
    {
        $stockCode = $request->getQueryParams()['q'] ?? null;

        if (is_null($stockCode)) {
            return $this->createErrorResponse($response, "Stock code is required.", 400);
        }

        $user = $request->getAttribute('user');
        $stockData = $this->stockService->getStockData($stockCode, $user->getId());

        if (empty($stockData)) {
            return $this->createErrorResponse($response, "Stock data not found.", 404);
        }

        $this->stockQueryService->create(
            stockCode: $stockCode,
            data: $stockData,
            userId: $user->getId()
        );

        try {
            $this->messageBrokerService->dispatchMessage(
                data: [
                    'type' => EMAIL_QUEUE_TYPE,
                    'data' => [
                        'email' => $user->getEmail(),
                        'stockCode' => $stockCode,
                        'stockData' => $stockData
                    ]
                ],
                queue: EMAIL_QUEUE
            );
        } catch (AMQPIOException $e) {
            // TODO: Add logs for the exception
        }

        $formattedData = $this->formatStockData($stockData);
        return $this->jsonResponse($response, $formattedData);
    }

    public function history(Request $request, Response $response, $args)
    {
        $user = $request->getAttribute('user');
        $history = $this->stockQueryService->getUserHistory($user->getId());
        $historyData = array_map(fn(StockQuery $s) => $s->getData(), $history);

        return $this->jsonResponse($response, $historyData);
    }

    private function formatStockData(array $data): array
    {
        return [
            'name' => $data['name'],
            'symbol' => $data['symbol'],
            'open' => (float) $data['open'],
            'high' => (float) $data['high'],
            'low' => (float) $data['low'],
            'close' => (float) $data['close'],
        ];
    }
}
