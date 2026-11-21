<?php

namespace App\Entities;

use DateTimeImmutable;

class UserEntity
{
    private ?int $id;
    private ?string $name;
    private string $login;
    private string $password;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        ?int $id,
        ?string $name,
        string $login,
        string $password,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->login = $login;
        $this->password = $password;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getPasswordHash(): string
    {
        return $this->password;
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
            $row['name'] ?? null,
            $row['login'],
            $row['password'],
            isset($row['created_at']) ? new DateTimeImmutable($row['created_at']) : null,
            isset($row['updated_at']) ? new DateTimeImmutable($row['updated_at']) : null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'login' => $this->login,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
