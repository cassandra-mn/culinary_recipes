<?php

use App\Controllers\UserController;

$router->post('/register', [UserController::class, 'create']);
$router->get('/users', [UserController::class, 'getAll']);
