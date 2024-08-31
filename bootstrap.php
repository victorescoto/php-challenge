<?php

declare(strict_types = 1);

use Dotenv\Dotenv;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/configs/constants.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$container = require_once __DIR__ . '/configs/container/bootstrap.php';

AppFactory::setContainer($container);

return AppFactory::create();
