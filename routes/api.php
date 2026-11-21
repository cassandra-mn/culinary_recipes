<?php

use App\Controllers\AuthController;
use App\Controllers\UserController;

$router->post('/login', [AuthController::class, 'login']);
$router->post('/register', [UserController::class, 'create']);
