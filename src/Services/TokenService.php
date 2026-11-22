<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use RuntimeException;

class TokenService
{
    public static function encode(int $userId): string
    {
        $jwtSecret = $_ENV['JWT_SECRET'];

        if (!$jwtSecret || strlen($jwtSecret) < 32) {
            throw new RuntimeException('JWT_SECRET não definido ou inválido.');
        }

        return JWT::encode(self::generate($userId), $jwtSecret, 'HS256');
    }

    public static function decode(string $jwt): array
    {
        return (array) JWT::decode($jwt, new Key($_ENV['JWT_SECRET'], 'HS256'));
    }

    private static function generate(int $userId): array
    {
        $now = time();

        return [
            'sub' => $userId,
            'iat' => $now,
            'exp' => $now + (int) $_ENV['JWT_TTL'],
            'jti' => bin2hex(random_bytes(16))
        ];
    }
}
