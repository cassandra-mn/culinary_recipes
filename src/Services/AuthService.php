<?php

namespace App\Services;

use App\Exceptions\LoginDataException;
use App\Exceptions\ValidationException;
use App\Repositories\RevokedTokenRepository;
use App\Repositories\UserRepository;

class AuthService
{
    private UserRepository $userRepository;
    private RevokedTokenRepository $revokedTokenRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->revokedTokenRepository = new RevokedTokenRepository();
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

    public function logout(string $token): void
    {
        $data = TokenService::decode($token);

        $this->revokedTokenRepository->create([
            'jti' => $data['jti'],
            'expires_at' => date('Y-m-d H:i:s', $data['exp']),
            'created_at' => date('Y-m-d H:i:s')
        ]);
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
