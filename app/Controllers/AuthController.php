<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;
use App\Services\JWTService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController extends BaseController
{
    public function __construct(
        private AuthService $authService,
        private JWTService $jwtService,
    ) {}

    public function login(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $email = $data['email'];
        $password = $data['password'];

        if (!$this->authService->validateCredentials($email, $password)) {
            return $this->createErrorResponse($response, 'Invalid credentials', 401);
        }

        $token = $this->jwtService->encode($email);
        return $this->jsonResponse($response, ['token' => $token]);
    }
}
