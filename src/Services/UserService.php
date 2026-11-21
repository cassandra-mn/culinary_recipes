<?php

namespace App\Services;

use App\Entities\UserEntity;
use App\Exceptions\LoginAlreadyExistsException;
use App\Exceptions\ValidationException;
use App\Repositories\UserRepository;
use Exception;

class UserService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function create(array $data): int
    {
        $this->validate($data);

        $existingUser = $this->userRepository->getByLogin($data['login']);

        if ($existingUser) {
            throw new LoginAlreadyExistsException('O login informado já está em uso.');
        }

        $user = new UserEntity(
            null,
            $data['name'] ?? null,
            $data['login'],
            password_hash($data['password'], PASSWORD_DEFAULT)
        );

        $userId = $this->userRepository->create($user);

        if (!$userId) {
            throw new Exception('Erro ao criar usuário.');
        }

        return $userId;
    }

    public function getAll(): array
    {
        return $this->userRepository->getAll();
    }

    private function validate(array $data): void
    {
        if (empty($data['login']) || empty($data['password'])) {
            throw new ValidationException('Os campos "login" e "password" são obrigatórios.');
        }

        if (mb_strlen($data['login']) > 100) {
            throw new ValidationException('O login deve ter no máximo 100 caracteres.');
        }

        if (isset($data['name']) && mb_strlen($data['name']) > 100) {
            throw new ValidationException('O nome deve ter no máximo 100 caracteres.');
        }

        if (strlen($data['password']) > 72) {
            throw new ValidationException('A senha é muito longa.');
        }
    }
}
