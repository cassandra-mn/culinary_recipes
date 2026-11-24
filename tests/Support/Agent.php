<?php

namespace Tests\Support;

final class Agent
{
    public function __construct(
        private readonly string $baseUrl
    ) { }

    public function get(string $path): TestRequest
    {
        return new TestRequest($this->baseUrl, 'GET', $path);
    }

    public function post(string $path): TestRequest
    {
        return new TestRequest($this->baseUrl, 'POST', $path);
    }

    public function patch(string $path): TestRequest
    {
        return new TestRequest($this->baseUrl, 'PATCH', $path);
    }

    public function delete(string $path): TestRequest
    {
        return new TestRequest($this->baseUrl, 'DELETE', $path);
    }
}
