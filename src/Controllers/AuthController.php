<?php

namespace App\Controllers;

use App\Exceptions\LoginDataException;
use App\Exceptions\ValidationException;
use App\Http\Request;
use App\Http\Response;
use App\Services\AuthService;
use Throwable;

class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function login(Request $request): Response
    {
        try {
            $data = $request->only([
                'login',
                'password'
            ]);

            $login = $this->authService->login($data);

            return Response::success($login, 'Usuário logado com sucesso.');
        } catch (LoginDataException $e) {
            return Response::error($e->getMessage(), 401);
        } catch (ValidationException $e) {
            return Response::error($e->getMessage(), 422);
        } catch (Throwable $e) {
            var_dump($e->getMessage());
            return Response::error('Erro ao realizar login.');
        }
    }
}
