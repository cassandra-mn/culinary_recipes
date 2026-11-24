<?php

namespace Tests\Support;

final class TestEnvironment
{
    public const DEFAULT_DATABASE = 'culinary_recipes_test';
    public const JWT_SECRET = 'segredo-de-teste-de-integracao-com-mais-de-32-caracteres';
    public const JWT_TTL = '3600';

    public static function database(): string
    {
        return $_ENV['DB_TEST_DATABASE'] ?? self::DEFAULT_DATABASE;
    }

    public static function variables(): array
    {
        return [
            'DB_HOST' => $_ENV['DB_HOST'] ?? '127.0.0.1',
            'DB_PORT' => $_ENV['DB_PORT'] ?? '3306',
            'DB_USERNAME' => $_ENV['DB_USERNAME'] ?? 'root',
            'DB_PASSWORD' => $_ENV['DB_PASSWORD'] ?? '',
            'DB_DATABASE' => self::database(),
            'JWT_SECRET' => self::JWT_SECRET,
            'JWT_TTL' => self::JWT_TTL,
        ];
    }
}
