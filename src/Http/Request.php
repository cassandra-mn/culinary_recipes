<?php

namespace App\Http;

class Request
{
    private array $get;
    private array $post;
    private array $server;
    private array $json;
    private array $headers;
    private ?int $userId;
    private array $params = [];

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = $_SERVER;
        $this->json = $this->parseJsonBody();
        $this->headers = getallheaders();
    }

    public function method(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function header(string $key): ?string
    {
        return $this->headers[$key] ?? null;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function param(string $key): ?string
    {
        return $this->params[$key] ?? null;
    }

    public function bearerToken(): ?string
    {
        $bearer = $this->header('Authorization');

        return $bearer ? trim(str_replace('Bearer', '', $bearer)) : null;
    }

    public function only(array $keys): array
    {
        $data = match ($this->method()) {
            'GET', 'HEAD' => $this->get,
            default => $this->post + $this->json
        };
        $filtered = [];

        foreach ($keys as $key) {
            if (array_key_exists($key, $data)) {
                $filtered[$key] = is_string($data[$key]) ? trim($data[$key]) : $data[$key];
            }
        }

        return $filtered;
    }

    private function parseJsonBody(): array
    {
        $contentType = $this->server['CONTENT_TYPE'] ?? '';

        if (!str_contains($contentType, 'application/json')) {
            return [];
        }

        $raw = file_get_contents('php://input');
        if ($raw === false || $raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }
}
