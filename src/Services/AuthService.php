<?php

namespace App\Services;

use App\Exceptions\LoginDataException;
use App\Exceptions\ValidationException;
use App\Repositories\UserRepository;

class AuthService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function login(array $data): array
    {
        $this->validate($data);

        $user = $this->userRepository->getByLogin($data['login']);

        if (!$user || !password_verify($data['password'], $user->getPasswordHash())) {
            throw new LoginDataException('Dados inválidos.');
        }

        return [
            'token' => TokenService::encode($user->getId()),
            'user' => $user->toArray()
        ];
    }

    private function validate(array $data): void
    {
        if (empty($data['login']) || empty($data['password'])) {
            throw new ValidationException('Os campos "login" e "password" são obrigatórios.');
        }

        if (mb_strlen($data['login']) > 100) {
            throw new ValidationException('O login deve ter no máximo 100 caracteres.');
        }

        if (strlen($data['password']) > 72) {
            throw new ValidationException('A senha é muito longa.');
        }
    }
}