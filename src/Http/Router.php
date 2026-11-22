<?php

namespace App\Http;

class Router
{
    protected array $routes = [];

    public function get(string $path, mixed $handler, array $middlewares = []): void
    {
        $this->make('GET', $path, $handler, $middlewares);
    }

    public function post(string $path, mixed $handler, array $middlewares = []): void
    {
        $this->make('POST', $path, $handler, $middlewares);
    }

    public function put(string $path, mixed $handler, array $middlewares = []): void
    {
        $this->make('PUT', $path, $handler, $middlewares);
    }

    public function delete(string $path, mixed $handler, array $middlewares = []): void
    {
        $this->make('DELETE', $path, $handler, $middlewares);
    }

    public function make(string $method, string $path, mixed $handler, array $middlewares = []): void
    {
        $this->routes[strtoupper($method)][$this->normalize($path)] = [
            'handler' => $handler,
            'middlewares' => $middlewares,
        ];
    }

    public function init(Request $request): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $path = $this->normalize(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');

        $route = $this->routes[$httpMethod][$path] ?? null;
        $handler = $route['handler'] ?? null;
        $middlewares = $route['middlewares'] ?? [];

        if ($handler === null) {
            http_response_code(404);
            echo json_encode(['error' => 'Rota não encontrada']);
            exit;
        }

        $next = fn (Request $request) => $this->call($handler, $request);

        foreach (array_reverse($middlewares) as $middlewareClass) {
            $next = fn (Request $request) => (new $middlewareClass())->handle($request, $next);
        }

        $response = $next($request);
        $this->send($response);
    }

    private function call($handler, Request $request)
    {
        [$class, $action] = $handler;

        $controller = new $class();
        $result = $controller->$action($request);

        if ($result instanceof Response) {
            $this->send($result);
        }
    }

    private function send(Response $response)
    {
        http_response_code($response->code ?? ($response->success ? 200 : 500));

        echo json_encode([
            'success' => $response->success,
            'data' => $response->data,
            'message' => $response->message
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    private function normalize(string $path): string
    {
        return rtrim($path, '/') ?: '/';
    }
}
