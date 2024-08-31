<?php

use App\Controllers\AuthController;
use App\Controllers\StockController;
use App\Controllers\UserController;
use App\Middlewares\JWTMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;
use App\Validators\UserCreateValidator;

return function (App $app) {
    $container = $app->getContainer();

    $app->post('/auth/login', AuthController::class . ':login');

    $app->post('/users', UserController::class . ':create')->add($container->get(UserCreateValidator::class));

    $app->group('', function (RouteCollectorProxy $group) {
        $group->get('/stock', StockController::class . ':stock');
        $group->get('/history', StockController::class . ':history');
    })->add($container->get(JWTMiddleware::class));
};
