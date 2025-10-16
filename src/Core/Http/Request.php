<?php

declare(strict_types=1);

namespace PigFarm\Core\Http;

class Request
{
    public function __construct(
        public string $method,
        public string $path,
        public array $query,
        public array $headers,
        public ?string $body
    ) {
    }

    public static function fromGlobals(): self
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = str_replace('_', '-', substr($key, 5));
                $headers[$name] = (string)$value;
            }
        }

        $body = file_get_contents('php://input') ?: null;
        return new self($method, $path, $_GET ?? [], $headers, $body);
    }
}
