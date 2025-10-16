<?php

declare(strict_types=1);

namespace PigFarm\Controllers;

use PigFarm\Core\Env;
use PigFarm\Core\Http\Request;
use PigFarm\Core\Http\Response;
use PigFarm\Services\PigAdvisoryService;
use PigFarm\Services\WeatherClient;

class ConditionsController
{
    public function index(Request $request): Response
    {
        $lat = (float)($request->query['lat'] ?? Env::get('DEFAULT_LAT', '52.52'));
        $lon = (float)($request->query['lon'] ?? Env::get('DEFAULT_LON', '13.405'));
        $days = (int)($request->query['days'] ?? Env::get('DEFAULT_DAYS', '3'));

        $page = max(1, (int)($request->query['page'] ?? 1));
        $perPage = (int)($request->query['perPage'] ?? 24);
        $perPage = max(1, min(72, $perPage));

        $filters = [
            'minTempC' => isset($request->query['minTempC']) ? (float)$request->query['minTempC'] : null,
            'maxTempC' => isset($request->query['maxTempC']) ? (float)$request->query['maxTempC'] : null,
            'minComfortIndex' => isset($request->query['minComfortIndex']) ? (int)$request->query['minComfortIndex'] : null,
            'maxComfortIndex' => isset($request->query['maxComfortIndex']) ? (int)$request->query['maxComfortIndex'] : null,
        ];

        try {
            $client = new WeatherClient();
            $raw = $client->fetchHourly($lat, $lon, $days);

            $service = new PigAdvisoryService();
            $records = $service->transform($raw);

            $records = array_values(array_filter($records, function ($r) use ($filters) {
                if ($filters['minTempC'] !== null && $r['temperatureC'] < $filters['minTempC']) return false;
                if ($filters['maxTempC'] !== null && $r['temperatureC'] > $filters['maxTempC']) return false;
                if ($filters['minComfortIndex'] !== null && $r['comfortIndex'] < $filters['minComfortIndex']) return false;
                if ($filters['maxComfortIndex'] !== null && $r['comfortIndex'] > $filters['maxComfortIndex']) return false;
                return true;
            }));

            $total = count($records);
            $offset = ($page - 1) * $perPage;
            $items = array_slice($records, $offset, $perPage);

            return Response::json([
                'meta' => [
                    'lat' => $lat,
                    'lon' => $lon,
                    'days' => $days,
                    'page' => $page,
                    'perPage' => $perPage,
                    'total' => $total,
                ],
                'items' => $items,
            ]);
        } catch (\Throwable $e) {
            return Response::json([
                'error' => 'Failed to fetch conditions',
                'message' => $e->getMessage(),
            ], 502);
        }
    }
}
