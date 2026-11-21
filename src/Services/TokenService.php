<?php

namespace App\Services;

use App\Exceptions\LoginDataException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TokenService
{
    public static function encode(int $userId): string
    {
        $jwtSecret = $_ENV['JWT_SECRET'];

        if (!$jwtSecret || strlen($jwtSecret) < 32) {
            throw new LoginDataException('JWT_SECRET não definido ou inválido.');
        }

        return JWT::encode(self::generate($userId), $jwtSecret, 'HS256');
    }

    private static function decode(string $jwt): array
    {
        return (array) JWT::decode($jwt, new Key($_ENV['JWT_SECRET'], 'HS256'));
    }

    private static function generate(int $userId): array
    {
        return [
            'sub' => $userId,
            'iat' => time(),
            'exp' => time() + (int) $_ENV['JWT_TTL']
        ];
    }
}
