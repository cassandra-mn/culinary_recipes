<?php

use App\Controllers\AuthController;
use App\Controllers\UserController;
use App\Middlewares\AuthMiddleware;

$router->post('/login', [AuthController::class, 'login']);
$router->post('/register', [UserController::class, 'create']);

$router->post('/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);
