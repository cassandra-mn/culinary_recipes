<?php

namespace App\Middlewares;

use App\Http\Request;
use App\Http\Response;
use App\Repositories\RevokedTokenRepository;
use App\Services\TokenService;
use Closure;
use Exception;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return Response::error('Usuário não autenticado.', 401);
        }

        try {
            $decoded = TokenService::decode($token);

            if ((new RevokedTokenRepository())->isRevoked($decoded['jti'])) {
                return Response::error('Usuário não autenticado.', 401);
            }

            $request->setUserId($decoded['sub']);
        } catch (Exception $e) {
            return Response::error('Usuário não autenticado.', 401);
        }

        return $next($request);
    }
}
