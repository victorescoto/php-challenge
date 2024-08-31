<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Services\JWTService;
use App\Services\UserService;
use Exception;
use Firebase\JWT\ExpiredException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;


class JWTMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly UserService $userService,
        private readonly JWTService $jwtService,
        private ResponseFactory $responseFactory,
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $jwt = $request->getHeader('Authorization')[0] ?? null;
        if (!$jwt) {
            return $this->errorResponse('Token not provided');
        }

        try {
            $tokenData = $this->jwtService->decode(str_replace('Bearer ', '', $jwt));
            if (!$tokenData) {
                throw new Exception;
            }
        } catch (ExpiredException $e) {
            return $this->errorResponse('Expired token');
        } catch (Exception $e) {
            return $this->errorResponse('Invalid token');
        }

        $user = $this->userService->getByEmail($tokenData['email']);
        $request = $request->withAttribute('user', $user);

        return $handler->handle($request);
    }

    private function errorResponse(string $message): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();
        $response
            ->getBody()
            ->write(json_encode(['error' => $message]));
        return $response->withStatus(401);
    }
}
