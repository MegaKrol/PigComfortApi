<?php

require 'vendor/autoload.php';

use PigFarm\Core\Env;
use PigFarm\Services\WeatherClient;
use PigFarm\Services\PigAdvisoryService;

Env::load(__DIR__);

echo "Testing WeatherClient...\n";

try {
    $client = new WeatherClient();
    echo "WeatherClient created successfully\n";
    
    $data = $client->fetchHourly(52.52, 13.405, 1);
    echo "Data fetched: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
    
    $service = new PigAdvisoryService();
    $transformed = $service->transform($data);
    echo "Transformed data count: " . count($transformed) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
