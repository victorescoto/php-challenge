<?php

declare(strict_types=1);

namespace App;

use PHPMailer\PHPMailer\PHPMailer;

class Config
{
    protected array $config = [];

    public function __construct(array $env)
    {
        $this->config = [
            'enviroment' => $env['APP_ENV'],
            'db' => [
                'host' => $env['DB_HOST'],
                'user' => $env['DB_USER'],
                'password' => $env['DB_PASS'],
                'dbname' => $env['DB_DATABASE'],
                'driver' => $env['DB_DRIVER'],
                'port' => $env['DB_PORT'],
            ],
            'jwt' => [
                'key' => $env['JWT_SECRET'],
                'algorithm' => $env['JWT_ALGORITHM'],
                'issuer' => $env['JWT_ISSUER'],
            ],
            'mailer' => [
                'host' => $env['MAIL_HOST'],
                'port' => $env['MAIL_PORT'],
                'smtpAuth' => true,
                'username' => $env['MAIL_USER'],
                'password' => $env['MAIL_PASS'],
                'smtpSecure' => PHPMailer::ENCRYPTION_STARTTLS,
            ],
            'messageBroker' => [
                'host' => $env['MESSAGE_BROKER_HOST'],
                'port' => $env['MESSAGE_BROKER_PORT'],
                'user' => $env['MESSAGE_BROKER_USER'],
                'password' => $env['MESSAGE_BROKER_PASS'],
            ],
        ];
    }

    public function __isset(string $name): bool
    {
        return isset($this->config[$name]);
    }

    public function __get(string $name)
    {
        return $this->config[$name] ?? null;
    }

    public function isDevMode(): bool
    {
        return $this->config['enviroment'] === 'development';
    }
}
