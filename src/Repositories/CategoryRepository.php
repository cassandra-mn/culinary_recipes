<?php

namespace App\Repositories;

use App\Entities\CategoryEntity;

class CategoryRepository extends BaseRepository
{
    public function table(): string
    {
        return 'categories';
    }

    public function fillable(): array
    {
        return [
            'name'
        ];
    }

    public function getAll(): array
    {
        return array_map(
            fn (array $row) => CategoryEntity::fromArray($row),
            $this->findAll()
        );
    }
}
