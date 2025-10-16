<?php

declare(strict_types=1);

namespace PigFarm\Services;

use GuzzleHttp\Client;
use PigFarm\Core\Env;

class WeatherClient
{
    private Client $http;
    private string $baseUrl;

    public function __construct(?Client $http = null)
    {
        $this->http = $http ?? new Client([
            'timeout' => 10,
            'verify' => false, // Disable SSL verification for Windows environments
        ]);
        $this->baseUrl = Env::get('OPEN_METEO_BASE', 'https://api.open-meteo.com/v1/forecast') ?? 'https://api.open-meteo.com/v1/forecast';
    }

    public function fetchHourly(float $lat, float $lon, int $days): array
    {
        $params = [
            'latitude' => $lat,
            'longitude' => $lon,
            'hourly' => 'temperature_2m,relative_humidity_2m',
            'forecast_days' => max(1, min(7, $days)),
            'timezone' => 'UTC',
        ];

        $response = $this->http->get($this->baseUrl, ['query' => $params]);
        $data = json_decode((string)$response->getBody(), true);
        return is_array($data) ? $data : [];
    }
}
