<?php

namespace App\Repositories;

use App\Entities\RecipeEntity;

class RecipeRepository extends BaseRepository
{
    public function table(): string
    {
        return 'recipes';
    }

    protected function fillable(): array
    {
        return [
            'user_id',
            'category_id',
            'name',
            'preparation_time',
            'preparation_method',
            'portions',
            'ingredients',
            'created_at',
            'updated_at',
        ];
    }

    public function create(RecipeEntity $recipe): int|false
    {
        return $this->insert([
            'user_id' => $recipe->getUserId(),
            'category_id' => $recipe->getCategoryId(),
            'name' => $recipe->getName(),
            'preparation_time' => $recipe->getPreparationTime(),
            'preparation_method' => $recipe->getPreparationMethod(),
            'portions' => $recipe->getPortions(),
            'ingredients' => $recipe->getIngredients(),
            'created_at' => $recipe->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $recipe->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    public function edit(RecipeEntity $recipe, int $id): int|false
    {
        return $this->update([
            'user_id' => $recipe->getUserId(),
            'category_id' => $recipe->getCategoryId(),
            'name' => $recipe->getName(),
            'preparation_time' => $recipe->getPreparationTime(),
            'preparation_method' => $recipe->getPreparationMethod(),
            'portions' => $recipe->getPortions(),
            'ingredients' => $recipe->getIngredients(),
            'created_at' => $recipe->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $recipe->getUpdatedAt()->format('Y-m-d H:i:s'),
        ], $id);
    }

    public function getById(string $value): ?RecipeEntity
    {
        $rows = $this->findBy('id', $value);

        return !empty($rows) ? RecipeEntity::fromArray($rows[0]) : null;
    }

    public function getAll(int $userId): array
    {
        return array_map(
            fn (array $row) => RecipeEntity::fromArray($row),
            $this->findAllByUser($userId) ?? []
        );
    }

    public function exclude(string $id): void
    {
        $this->delete($id);
    }
}
