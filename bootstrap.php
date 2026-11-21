<?php
require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use App\Database\Connection;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$dbConnection = (new Connection())->getConnection();
