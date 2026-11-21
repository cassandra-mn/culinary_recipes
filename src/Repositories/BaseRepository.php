<?php

namespace App\Repositories;

use App\Database\Connection;
use PDO;

abstract class BaseRepository
{
    protected PDO $db;

    abstract public function table(): string;

    public function __construct()
    {
        $this->db = (new Connection())->getConnection();
    }
}
