<?php

declare(strict_types=1);

namespace PigFarm\Core;

use PigFarm\Core\Http\Request;
use PigFarm\Core\Http\Response;

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
    ];

    public function get(string $path, callable|array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function dispatch(Request $request): Response
    {
        $method = strtoupper($request->method);
        $path = $request->path;

        $handler = $this->routes[$method][$path] ?? null;
        if ($handler === null) {
            return Response::json(['error' => 'Not Found'], 404);
        }

        if (is_array($handler)) {
            [$class, $action] = $handler;
            $controller = new $class();
            return $controller->$action($request);
        }

        return $handler($request);
    }
}
