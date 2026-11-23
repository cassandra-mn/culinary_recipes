<?php

namespace App\Services;

use App\Entities\RecipeEntity;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Repositories\RecipeRepository;
use DateTimeImmutable;
use Exception;

class RecipeService
{
    private RecipeRepository $recipeRepository;

    public function __construct()
    {
        $this->recipeRepository = new RecipeRepository();
    }

    public function create(array $data, int $userId): RecipeEntity
    {
        $this->validate($data);

        $recipe = new RecipeEntity(
            null,
            $userId,
            $data['category_id'] ?? null,
            $data['name'] ?? null,
            $data['preparation_time'] ?? null,
            $data['preparation_method'],
            $data['portions'] ?? null,
            $data['ingredients'] ?? null,
            null,
            null
        );

        $recipeId = $this->recipeRepository->create($recipe);

        if (!$recipeId) {
            throw new Exception('Erro ao criar receita.');
        }

        return $recipe->setId($recipeId);
    }

    public function update(array $data, int $id, int $userId): RecipeEntity
    {
        $recipe = $this->getById($id, $userId);

        $this->validate($data, false);

        $updated = new RecipeEntity(
            $recipe->getId(),
            $userId,
            array_key_exists('category_id', $data) ? $data['category_id'] : $recipe->getCategoryId(),
            $data['name'] ?? $recipe->getName(),
            $data['preparation_time'] ?? $recipe->getPreparationTime(),
            $data['preparation_method'] ?? $recipe->getPreparationMethod(),
            $data['portions'] ?? $recipe->getPortions(),
            $data['ingredients'] ?? $recipe->getIngredients(),
            $recipe->getCreatedAt(),
            new DateTimeImmutable()
        );

        $this->recipeRepository->edit($updated, $id);

        return $updated;
    }

    public function getById(int $id, int $userId): ?RecipeEntity
    {
        $recipe = $this->recipeRepository->getById($id);

        if (!$recipe || $recipe->getUserId() !== $userId) {
            throw new NotFoundException('Receita não encontrada.');
        }

        return $recipe;
    }

    public function getAll(int $userId): array
    {
        return $this->recipeRepository->getAll($userId);
    }

    public function exclude(int $id, int $userId): void
    {
        $this->getById($id, $userId);

        $this->recipeRepository->exclude($id);
    }

    private function validate(array $data, bool $isCreate = true): void
    {
        if ($isCreate && empty($data['preparation_method'])) {
            throw new ValidationException('O campo "preparation_method" é obrigatório.');
        }

        if (isset($data['name']) && mb_strlen($data['name']) > 45) {
            throw new ValidationException('O nome deve ter no máximo 45 caracteres.');
        }
    }
}
