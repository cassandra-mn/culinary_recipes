<?php

namespace App\Controllers;

use App\Entities\CategoryEntity;
use App\Http\Response;
use App\Services\CategoryService;
use Throwable;

class CategoryController
{
    private CategoryService $categoryService;

    public function __construct()
    {
        $this->categoryService = new CategoryService();
    }

    public function getAll(): Response
    {
        try {
            $categories = array_map(
                fn (CategoryEntity $category) => $category->toArray(),
                $this->categoryService->getAll()
            );

            return Response::success($categories, 'Categorias encontradas com sucesso.');
        } catch (Throwable $e) {
            return Response::error('Erro ao buscar categorias.');
        }
    }
}
