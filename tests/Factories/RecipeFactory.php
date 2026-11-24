<?php

namespace Tests\Factories;

use Faker\Factory;
use Tests\Support\TestDatabase;

final class RecipeFactory
{
    public static function createRecipeInfo(?int $categoryId = 1): array
    {
        $faker = Factory::create();

        return [
            'category_id' => $categoryId,
            'name' => $faker->words(3, true),
            'preparation_time' => $faker->numberBetween(5, 180),
            'preparation_method' => $faker->paragraph(),
            'portions' => $faker->numberBetween(1, 12),
            'ingredients' => implode(', ', $faker->words(5))
        ];
    }

    public static function createRecipe(int $userId, array $overrides = []): array
    {
        $data = array_merge(self::createRecipeInfo(), $overrides);
        $now = date('Y-m-d H:i:s');

        $pdo = TestDatabase::connection();
        $statement = $pdo->prepare('INSERT INTO recipes
            (user_id, category_id, name, preparation_time, preparation_method, portions, ingredients, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');

        $statement->execute([
            $userId,
            $data['category_id'],
            $data['name'],
            $data['preparation_time'],
            $data['preparation_method'],
            $data['portions'],
            $data['ingredients'],
            $now,
            $now
        ]);

        return $data + ['id' => (int) $pdo->lastInsertId(), 'user_id' => $userId];
    }
}
