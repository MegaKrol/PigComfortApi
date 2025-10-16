<?php

declare(strict_types=1);

namespace PigFarm\Controllers;

use PigFarm\Core\Http\Request;
use PigFarm\Core\Http\Response;

class HealthController
{
    public function index(Request $request): Response
    {
        return Response::json([
            'status' => 'ok',
            'service' => 'pig-farm-api',
            'time' => gmdate('c'),
        ]);
    }
}
