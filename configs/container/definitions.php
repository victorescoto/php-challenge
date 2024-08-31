<?php

declare(strict_types=1);

use App\Config;
use App\Middlewares\JWTMiddleware;
use App\Services\JWTService;
use App\Services\MessageBrokerService;
use App\Services\UserService;
use DI\Container;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use PHPMailer\PHPMailer\PHPMailer;
use Slim\Psr7\Factory\ResponseFactory;
use function DI\create;


return [
    Config::class => create(Config::class)->constructor($_ENV),

    EntityManager::class => static function (Config $config) {
        $ormConfig = ORMSetup::createAttributeMetadataConfiguration(
            paths: [PROJECT_ROOT . '/app/Entities'],
            isDevMode: true,
        );

        $connection = DriverManager::getConnection(
            params: $config->db,
            config: $ormConfig
        );

        return new EntityManager(conn: $connection, config: $ormConfig);
    },

    UserService::class => static function (Container $c) {
        return new UserService($c->get(EntityManager::class));
    },

    JWTService::class => static function (Config $config) {
        return new JWTService(
            key: $config->jwt['key'],
            algorithm: $config->jwt['algorithm'],
            issuer: $config->jwt['issuer']
        );
    },

    JWTMiddleware::class => static function (Container $c) {
        return new JWTMiddleware(
            userService: $c->get(UserService::class),
            jwtService: $c->get(JWTService::class),
            responseFactory: $c->get(ResponseFactory::class),
        );
    },

    PHPMailer::class => static function (Config $config) {
        $mailer = new PHPMailer();
        $mailer->isSMTP();
        $mailer->Host = $config->mailer['host'];
        $mailer->Port = $config->mailer['port'];

        if (! $config->isDevMode()) {
            $mailer->SMTPAuth = $config->mailer['smtpAuth'];
            $mailer->Username = $config->mailer['username'];
            $mailer->Password = $config->mailer['password'];
            $mailer->SMTPSecure = $config->mailer['smtpSecure'];
        }

        return $mailer;
    },

    MessageBrokerService::class => static function (Config $config) {
        return new MessageBrokerService(
            host: $config->messageBroker['host'],
            port: $config->messageBroker['port'],
            user: $config->messageBroker['user'],
            password: $config->messageBroker['password'],
        );
    },
];
