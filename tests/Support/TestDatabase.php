<?php

namespace Tests\Support;

use PDO;
use RuntimeException;

final class TestDatabase
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection === null) {
            $env = TestEnvironment::variables();

            self::$connection = new PDO(
                "mysql:host={$env['DB_HOST']};port={$env['DB_PORT']};charset=utf8mb4;dbname={$env['DB_DATABASE']}",
                $env['DB_USERNAME'],
                $env['DB_PASSWORD'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
            );
        }

        return self::$connection;
    }

    public static function truncate(): void
    {
        $database = TestEnvironment::database();

        if (!str_ends_with($database, '_test')) {
            throw new RuntimeException("Recusando truncar o banco '{$database}'.");
        }

        $pdo = self::connection();

        $pdo->exec('SET FOREIGN_KEY_CHECKS=0');

        foreach (['recipes', 'users', 'revoked_tokens'] as $table) {
            $pdo->exec("TRUNCATE TABLE `{$table}`");
        }

        $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    }

    public static function disconnect(): void
    {
        self::$connection = null;
    }
}
