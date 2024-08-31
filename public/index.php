<?php

declare(strict_types=1);

use App\Middlewares\JSONResponseHeaderMiddleware;

$app = require_once __DIR__ . '/../bootstrap.php';
$container = $app->getContainer();
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

$app->add($container->get(JSONResponseHeaderMiddleware::class));

$router = require_once __DIR__ . '/../configs/routes.php';
$router($app);

$app->run();
