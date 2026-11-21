<?php

namespace App\Http;

class Router
{
    protected array $routes = [];

    public function get(string $path, $handler): void
    {
        $this->make('GET', $path, $handler);
    }

    public function post(string $path, $handler): void
    {
        $this->make('POST', $path, $handler);
    }

    public function put(string $path, $handler): void
    {
        $this->make('PUT', $path, $handler);
    }

    public function delete(string $path, $handler): void
    {
        $this->make('DELETE', $path, $handler);
    }

    public function make(string $method, string $path, $handler): void
    {
        $this->routes[strtoupper($method)][$this->normalize($path)] = $handler;
    }

    public function init(Request $request): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $path = $this->normalize(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');

        $handler = $this->routes[$httpMethod][$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            echo json_encode(['error' => 'Rota não encontrada']);
            exit;
        }

        $this->call($handler, $request);
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
