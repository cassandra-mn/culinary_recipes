<?php

namespace Tests\Factories;

use Faker\Factory;
use Tests\Support\TestDatabase;

final class UserFactory
{
    public static function createCredentials(): array
    {
        $faker = Factory::create();

        return [
            'login' => $faker->email(),
            'password' => $faker->password(8, 20)
        ];
    }

    public static function createUser(): array
    {
        $data = self::createCredentials();
        $now = date('Y-m-d H:i:s');

        $pdo = TestDatabase::connection();
        $statement = $pdo->prepare(
            'INSERT INTO users (name, login, password, created_at, updated_at) VALUES (?, ?, ?, ?, ?)'
        );
        $statement->execute([
            Factory::create()->name(),
            $data['login'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $now,
            $now
        ]);

        return $data + ['id' => (int) $pdo->lastInsertId()];
    }
}
