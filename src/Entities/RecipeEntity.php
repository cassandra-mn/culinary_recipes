<?php

namespace App\Entities;

use DateTimeImmutable;

class RecipeEntity
{
    private ?int $id;
    private int $userId;
    private ?int $categoryId;
    private ?string $name;
    private ?int $preparationTime;
    private string $preparationMethod;
    private ?int $portions;
    private ?string $ingredients;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        ?int $id,
        int $userId,
        ?int $categoryId,
        ?string $name,
        ?int $preparationTime,
        string $preparationMethod,
        ?int $portions,
        ?string $ingredients,
        ?DateTimeImmutable $createdAt,
        ?DateTimeImmutable $updatedAt,
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->categoryId = $categoryId;
        $this->name = $name;
        $this->preparationTime = $preparationTime;
        $this->preparationMethod = $preparationMethod;
        $this->portions = $portions;
        $this->ingredients = $ingredients;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPreparationTime(): ?int
    {
        return $this->preparationTime;
    }

    public function getPreparationMethod(): string
    {
        return $this->preparationMethod;
    }

    public function getPortions(): ?int
    {
        return $this->portions;
    }

    public function getIngredients(): ?string
    {
        return $this->ingredients;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public static function fromArray(array $row): self
    {
        return new self(
            (int) $row['id'],
            (int) $row['user_id'],
            !empty($row['category_id']) ? (int) $row['category_id']: null,
            $row['name'] ?? null,
            $row['preparation_time'] ?? null,
            $row['preparation_method'] ?? null,
            $row['portions'] ?? null,
            $row['ingredients'] ?? null,
            isset($row['created_at']) ? new DateTimeImmutable($row['created_at']) : null,
            isset($row['updated_at']) ? new DateTimeImmutable($row['updated_at']) : null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'category_id' => $this->categoryId,
            'name' => $this->name,
            'preparation_time' => $this->preparationTime,
            'preparation_method' => $this->preparationMethod,
            'portions' => $this->portions,
            'ingredients' => $this->ingredients,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
