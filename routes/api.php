<?php

use App\Controllers\AuthController;
use App\Controllers\CategoryController;
use App\Controllers\UserController;
use App\Middlewares\AuthMiddleware;

$router->post('/login', [AuthController::class, 'login']);
$router->post('/register', [UserController::class, 'create']);

$router->group(['middlewares' => [AuthMiddleware::class]], function ($router) {
    $router->post('/logout', [AuthController::class, 'logout']);

    $router->get('/categories', [CategoryController::class, 'getAll']);
});
