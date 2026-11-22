<?php

namespace App\Repositories;

use App\Entities\UserEntity;

class UserRepository extends BaseRepository
{
    public function table(): string
    {
        return 'users';
    }

    public function fillable(): array
    {
        return [
            'name',
            'login',
            'password',
            'created_at',
            'updated_at',
        ];
    }

    public function create(UserEntity $user): int|false
    {
        return $this->insert([
            'name' => $user->getName(),
            'login' => $user->getLogin(),
            'password' => $user->getPasswordHash(),
            'created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $user->getUpdatedAt()->format('Y-m-d H:i:s'),
        ]);
    }

    public function getByLogin(string $value): ?UserEntity
    {
        $rows = $this->findBy('login', $value);

        return !empty($rows) ? UserEntity::fromArray($rows[0]) : null;
    }

    public function getAll(): array
    {
        return array_map(
            fn (array $row) => UserEntity::fromArray($row),
            $this->findAll()
        );
    }
}
