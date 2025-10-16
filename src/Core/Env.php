<?php

declare(strict_types=1);

namespace PigFarm\Core;

use Dotenv\Dotenv;

class Env
{
    public static function load(string $basePath): void
    {
        if (is_readable($basePath . '/.env')) {
            $dotenv = Dotenv::createImmutable($basePath);
            $dotenv->safeLoad();
        }
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        if ($value === false || $value === null || $value === '') {
            return $default;
        }
        return (string)$value;
    }
}
