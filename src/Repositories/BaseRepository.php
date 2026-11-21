<?php

namespace App\Repositories;

use App\Database\Connection;
use Exception;
use PDO;
use PDOException;

abstract class BaseRepository
{
    protected PDO $db;

    abstract public function table(): string;

    public function __construct()
    {
        $this->db = (new Connection())->getConnection();
    }

    public function findAll()
    {
        try {
            $statement = "SELECT * FROM {$this->table()}";

            return $this->db
                ->query($statement)
                ->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception(
                "Erro ao buscar os registros de {$this->table()}: " . $e->getMessage()
            );
        }
    }

    public function findBy(string $field, mixed $value): ?array
    {
        try {
            $statement = "SELECT * FROM {$this->table()} WHERE $field = ?";

            $query = $this->db->prepare($statement);
            $query->execute([$value]);

            return $query->fetchAll(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            throw new Exception(
                "Erro ao buscar registro com $field = $value de {$this->table()}."
            );
        }
    }

    public function insert(array $data): int|false
    {
        try {
            $columns = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));

            $statement = "INSERT INTO {$this->table()} ($columns) VALUES ($placeholders)";

            $query = $this->db->prepare($statement);
            $result = $query->execute(array_values($data));

            return $result ? $this->db->lastInsertId() : false;
        } catch (PDOException $e) {
            throw new Exception(
                "Erro ao inserir registro em {$this->table()}."
            );
        }
    }
}
