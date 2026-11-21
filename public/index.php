<?php

require __DIR__ . '/../bootstrap.php';

$request = new App\Http\Request();
$router = new App\Http\Router();

require __DIR__ . '/../routes/api.php';

$router->init($request);
