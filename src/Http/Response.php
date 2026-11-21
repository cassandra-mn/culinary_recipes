<?php

namespace App\Http;

class Response
{
    public function __construct(
        public bool $success,
        public mixed $data = null,
        public ?string $message = null,
        public ?int $code = null,
    ) {}

    public static function success(mixed $data = null, string $message = '', int $code = 200): self
    {
        return new self(true, $data, $message, $code);
    }

    public static function error(string $message = '', int $code = 500, mixed $data = null): self
    {
        return new self(false, $data, $message, $code);
    }
}
