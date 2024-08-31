<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\UserService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;

class UserController extends BaseController
{
    public function __construct(private UserService $userService) {}

    public function create(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        try {
            $user = $this->userService->create($data['email'], $data['password']);
        } catch (UniqueConstraintViolationException $e) {
            return $this->createErrorResponse($response, "User already exists.", 400);
        } catch (Exception $e) {
            return $this->createErrorResponse($response, $e->getMessage(), 500);
        }

        return $this->jsonResponse($response, $user);
    }

    public function show(Request $request, Response $response, $args)
    {
        $user = $request->getAttribute('user');
        return $this->jsonResponse($response, $user);
    }
}
