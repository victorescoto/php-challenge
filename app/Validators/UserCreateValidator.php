<?php

declare(strict_types=1);

namespace App\Validators;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;

class UserCreateValidator implements MiddlewareInterface
{
    public function __construct(private ResponseFactory $responseFactory) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $data = $request->getParsedBody();
        if (!isset($data['email']) || !isset($data['password'])) {
            return $this->errorResponse('Email and password are required');
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->errorResponse('Invalid email');
        }

        return $handler->handle($request);
    }

    private function errorResponse(string $message): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();
        $response
            ->getBody()
            ->write(json_encode(['error' => $message]));
        return $response->withStatus(400);
    }
}
