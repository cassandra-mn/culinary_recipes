<?php

namespace App\Repositories;

class RevokedTokenRepository extends BaseRepository
{
    public function table(): string
    {
        return 'revoked_tokens';
    }

    public function fillable(): array
    {
        return [];
    }

    public function create(array $data): int|false
    {
        return $this->insert([
            'jti' => $data['jti'],
            'expires_at' => $data['expires_at'],
            'created_at' => $data['created_at'],
        ]);
    }

    public function isRevoked(string $jti): bool
    {
        $rows = $this->findBy('jti', $jti);

        return !empty($rows);
    }
}
