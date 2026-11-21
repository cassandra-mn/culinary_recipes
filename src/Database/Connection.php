<?php

namespace App\Database;

use Exception;
use PDO;
use PDOException;

class Connection
{
    private ?PDO $dbConnection = null;

    public function __construct()
    {
        $host = $_ENV['DB_HOST'];
        $port = $_ENV['DB_PORT'];
        $db = $_ENV['DB_DATABASE'];
        $user = $_ENV['DB_USERNAME'];
        $pass = $_ENV['DB_PASSWORD'];

        try {
            $uri = "mysql:host=$host;port=$port;charset=utf8mb4;dbname=$db";

            $this->dbConnection = new PDO($uri, $user, $pass);
        } catch (PDOException $e) {
            throw new Exception("Ocorreu um erro, tente novamente mais tarde.");
        }
    }

    public function getConnection(): PDO
    {
        return $this->dbConnection;
    }
}
