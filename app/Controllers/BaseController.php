<?php

namespace App\Controllers;

use Doctrine\Common\Collections\Collection;
use JsonSerializable;
use Psr\Http\Message\ResponseInterface as Response;

abstract class BaseController
{
    protected function jsonResponse(
        Response $response,
        array | JsonSerializable | Collection $data,
        int $status = 200
    ): Response {
        $response->getBody()->write(json_encode($data));
        return $response->withStatus($status);
    }

    protected function createErrorResponse(Response $response, string $message, int $status): Response
    {
        return $this->jsonResponse($response, ['error' => $message], $status);
    }
}
