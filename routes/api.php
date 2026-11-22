<?php

use App\Controllers\AuthController;
use App\Controllers\CategoryController;
use App\Controllers\RecipeController;
use App\Controllers\UserController;
use App\Middlewares\AuthMiddleware;

$router->post('/login', [AuthController::class, 'login']);
$router->post('/register', [UserController::class, 'create']);

$router->group(['middlewares' => [AuthMiddleware::class]], function ($router) {
    $router->post('/logout', [AuthController::class, 'logout']);

    $router->get('/categories', [CategoryController::class, 'getAll']);

    $router->get('/recipes', [RecipeController::class, 'getAll']);
    $router->post('/recipes', [RecipeController::class, 'create']);
    $router->get('/recipes/{id}', [RecipeController::class, 'getById']);
    $router->patch('/recipes/{id}', [RecipeController::class, 'update']);
    $router->delete('/recipes/{id}', [RecipeController::class, 'exclude']);
});
