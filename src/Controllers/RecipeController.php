<?php

namespace App\Controllers;

use App\Entities\RecipeEntity;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Http\Request;
use App\Http\Response;
use App\Services\RecipeService;
use Throwable;

class RecipeController
{
    private RecipeService $recipeService;

    public function __construct()
    {
        $this->recipeService = new RecipeService();
    }

    public function create(Request $request): Response
    {
        try {
            $data = $request->only([
                'category_id',
                'name',
                'preparation_time',
                'preparation_method',
                'portions',
                'ingredients'
            ]);

            $id = $this->recipeService->create($data, $request->getUserId());

            return Response::success($id, 'Receita criada com sucesso.', 201);
        } catch (ValidationException $e) {
            return Response::error($e->getMessage(), 422);
        } catch (Throwable $e) {
            return Response::error('Erro ao criar receita.');
        }
    }

    public function update(Request $request): Response
    {
        try {
            $id = (int) $request->param('id');

            $data = $request->only([
                'category_id',
                'name',
                'preparation_time',
                'preparation_method',
                'portions',
                'ingredients'
            ]);

            $id = $this->recipeService->update($data, $id, $request->getUserId());

            return Response::success($id, 'Receita atualizada com sucesso.');
        } catch (NotFoundException $e) {
            return Response::error($e->getMessage(), 404);
        } catch (ValidationException $e) {
            return Response::error($e->getMessage(), 422);
        } catch (Throwable $e) {
            return Response::error('Erro ao atualizar receita.');
        }
    }

    public function getAll(Request $request): Response
    {
        try {
            $recipes = array_map(
                fn (RecipeEntity $recipe) => $recipe->toArray(),
                $this->recipeService->getAll($request->getUserId())
            );

            return Response::success($recipes, 'Receitas encontradas com sucesso.');
        } catch (Throwable $e) {
            return Response::error('Erro ao buscar receitas.');
        }
    }

    public function getById(Request $request): Response
    {
        try {
            $id = (int) $request->param('id');

            $recipe = $this->recipeService->getById($id, $request->getUserId());

            return Response::success($recipe->toArray(), 'Receita encontrada com sucesso.');
        } catch (NotFoundException $e) {
            return Response::error($e->getMessage(), 404);
        } catch (Throwable $e) {
            return Response::error('Erro ao buscar receita.');
        }
    }

    public function exclude(Request $request): Response
    {
        try {
            $id = (int) $request->param('id');

            $this->recipeService->exclude($id, $request->getUserId());

            return Response::success(null, 'Receita deletada com sucesso.');
        } catch (NotFoundException $e) {
            return Response::error($e->getMessage(), 404);
        } catch (Throwable $e) {
            return Response::error('Erro ao deletar receita.');
        }
    }
}
