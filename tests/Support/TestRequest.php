<?php

namespace Tests\Support;

use LogicException;
use RuntimeException;

final class TestRequest
{
    private array $headers = [];
    private ?array $payload = null;
    private ?array $response = null;

    public function __construct(
        private readonly string $baseUrl,
        private readonly string $method,
        private readonly string $path
    ) { }

    public function send(array $body): self
    {
        $this->assertNotSent();
        $this->payload = $body;

        return $this;
    }

    public function set(string $name, string $value): self
    {
        $this->assertNotSent();
        $this->headers[$name] = $value;

        return $this;
    }

    public function status(): int
    {
        return $this->response()['status'];
    }

    public function body(): array
    {
        return $this->response()['body'];
    }

    public function text(): string
    {
        return $this->response()['text'];
    }

    private function response(): array
    {
        return $this->response ??= $this->execute();
    }

    private function execute(): array
    {
        $headers = [];
        foreach ($this->headers as $name => $value) {
            $headers[] = "{$name}: {$value}";
        }

        $options = [
            CURLOPT_URL => $this->baseUrl . $this->path,
            CURLOPT_CUSTOMREQUEST => $this->method,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
        ];

        if ($this->payload !== null) {
            $headers[] = 'Content-Type: application/json';
            $options[CURLOPT_POSTFIELDS] = json_encode($this->payload === [] ? new \stdClass() : $this->payload);
        }

        $options[CURLOPT_HTTPHEADER] = $headers;

        $curl = curl_init();
        curl_setopt_array($curl, $options);

        $text = curl_exec($curl);

        if ($text === false) {
            $error = curl_error($curl);
            curl_close($curl);

            throw new RuntimeException("Falha na requisição {$this->method} {$this->path}: {$error}");
        }

        $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);

        $body = json_decode($text, true);

        return ['status' => $status, 'text' => $text, 'body' => is_array($body) ? $body : []];
    }

    private function assertNotSent(): void
    {
        if ($this->response !== null) {
            throw new LogicException('A requisição já foi enviada.');
        }
    }
}
