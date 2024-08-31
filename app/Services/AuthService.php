<?php

declare(strict_types=1);

namespace App\Services;

class AuthService
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function validateCredentials(string $email, string $password): bool
    {
        $user = $this->userService->getByEmail($email);

        if (!$user) {
            return false;
        }

        return password_verify($password, $user->getPassword());
    }
}
