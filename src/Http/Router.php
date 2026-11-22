<?php

namespace App\Http;

class Router
{
    protected array $routes = [];
    protected array $groupMiddlewares = [];

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

    public function patch(string $path, mixed $handler, array $middlewares = []): void
    {
        $this->make('PATCH', $path, $handler, $middlewares);
    }

    public function delete(string $path, mixed $handler, array $middlewares = []): void
    {
        $this->make('DELETE', $path, $handler, $middlewares);
    }

    public function make(string $method, string $path, mixed $handler, array $middlewares = []): void
    {
        $this->routes[strtoupper($method)][$this->normalize($path)] = [
            'handler' => $handler,
            'middlewares' => array_merge($this->groupMiddlewares, $middlewares),
        ];
    }

    public function group(array $options, callable $callback): void
    {
        $previous = $this->groupMiddlewares;

        $this->groupMiddlewares = array_merge($previous, $options['middlewares'] ?? []);

        $callback($this);

        $this->groupMiddlewares = $previous;
    }

    public function init(Request $request): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $path = $this->normalize(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
        $found = $this->match($request->method(), $path);

        if ($found === null) {
            http_response_code(404);
            echo json_encode(['error' => 'Rota não encontrada']);
            exit;
        }

        $route = $found['route'];
        $request->setParams($found['params']);

        $next = fn (Request $request) => $this->call($route['handler'], $request);

        foreach (array_reverse($route['middlewares'] ?? []) as $middlewareClass) {
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

    private function match(string $method, string $path): ?array
    {
        foreach ($this->routes[$method] ?? [] as $pattern => $route) {
            $regex = '#^' . preg_replace('#\{(\w+)}#', '(?P<$1>[^/]+)', $pattern) . '$#';

            if (preg_match($regex, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                return ['route' => $route, 'params' => $params];
            }
        }

        return null;
    }
}
