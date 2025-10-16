<?php

declare(strict_types=1);

use PigFarm\Core\Env;
use PigFarm\Core\Http\Request;
use PigFarm\Core\Http\Response;
use PigFarm\Core\Router;

require __DIR__ . '/../vendor/autoload.php';

Env::load(__DIR__ . '/../');

$request = Request::fromGlobals();
$router = new Router();

// Health
$router->get('/health', [PigFarm\Controllers\HealthController::class, 'index']);

// Conditions
$router->get('/api/conditions', [PigFarm\Controllers\ConditionsController::class, 'index']);

$response = $router->dispatch($request);

if ($response instanceof Response) {
    $response->send();
} else {
    (new Response(500, ['Content-Type' => 'application/json'], json_encode(['error' => 'Invalid response'])))->send();
}
