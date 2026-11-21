<?php

namespace App\Controllers;

use App\Entities\UserEntity;
use App\Exceptions\LoginAlreadyExistsException;
use App\Exceptions\ValidationException;
use App\Http\Request;
use App\Http\Response;
use App\Services\UserService;
use Throwable;

class UserController
{
    private UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
    }

    public function create(Request $request): Response
    {
        try {
            $data = $request->only([
                'name',
                'login',
                'password'
            ]);

            $id = $this->userService->create($data);

            return Response::success($id, 'Usuário criado com sucesso.', 201);
        } catch (LoginAlreadyExistsException $e) {
            return Response::error($e->getMessage(), 409);
        } catch (ValidationException $e) {
            return Response::error($e->getMessage(), 422);
        } catch (Throwable $e) {
            return Response::error('Erro ao criar usuário.');
        }
    }

    public function getAll(): Response
    {
        try {
            $users = array_map(
                fn (UserEntity $user) => $user->toArray(),
                $this->userService->getAll()
            );

            return Response::success(
                $users,
                'Usuários encontrados com sucesso.'
            );
        } catch (Throwable $e) {
            return Response::error('Erro ao buscar usuários.');
        }
    }
}
