<?php

declare(strict_types=1);

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTService
{
    public function __construct(
        private readonly string $key,
        private readonly string $algorithm,
        private readonly string $issuer
    ) {}

    public function encode(string $email)
    {
        $time = time();
        $payload = [
            'iat' => $time,
            'nbf' => $time,
            'exp' => $time + 3600,
            'data' => [
                'email' => $email
            ]
        ];

        return JWT::encode($payload, $this->key, $this->algorithm);
    }

    public function decode(string $jwt)
    {
        $decoded = JWT::decode($jwt, new Key($this->key, $this->algorithm));
        return (array) $decoded->data;
    }
}
