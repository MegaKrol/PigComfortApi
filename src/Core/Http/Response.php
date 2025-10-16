<?php

declare(strict_types=1);

namespace PigFarm\Core\Http;

class Response
{
    public function __construct(
        public int $status,
        public array $headers,
        public string $body
    ) {
    }

    public static function json(array $data, int $status = 200): self
    {
        return new self($status, ['Content-Type' => 'application/json'], json_encode($data));
    }

    public function send(): void
    {
        http_response_code($this->status);
        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }
        echo $this->body;
    }
}
