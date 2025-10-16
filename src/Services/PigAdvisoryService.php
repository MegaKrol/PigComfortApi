<?php

declare(strict_types=1);

namespace PigFarm\Services;

class PigAdvisoryService
{
    /**
     * Transform Open-Meteo hourly data into pig-farm oriented records with comfort index and advisory.
     */
    public function transform(array $weather): array
    {
        $hourly = $weather['hourly'] ?? [];
        $times = $hourly['time'] ?? [];
        $temps = $hourly['temperature_2m'] ?? [];
        $humidity = $hourly['relative_humidity_2m'] ?? [];

        $records = [];
        $count = min(count($times), count($temps), count($humidity));
        for ($i = 0; $i < $count; $i++) {
            $t = (float)$temps[$i];
            $h = (float)$humidity[$i];
            $comfort = $this->computeComfortIndex($t, $h);
            $advisory = $this->advisoryFor($comfort, $t, $h);
            $records[] = [
                'timeUtc' => $times[$i],
                'temperatureC' => $t,
                'relativeHumidity' => $h,
                'comfortIndex' => $comfort,
                'advisory' => $advisory,
            ];
        }
        return $records;
    }

    private function computeComfortIndex(float $tempC, float $rh): int
    {
        // Simple heuristic: ideal ~ 18C-22C and RH 50-65%
        $tempScore = 100 - abs($tempC - 20) * 6; // drops ~6 per degree away from 20C
        $rhScore = 100 - abs($rh - 57.5) * 2; // drops ~2 per % away from 57.5%
        $score = max(0, min(100, (int)round(($tempScore * 0.6 + $rhScore * 0.4))));
        return $score;
    }

    private function advisoryFor(int $comfort, float $tempC, float $rh): string
    {
        if ($comfort >= 80) {
            return 'Optimal conditions';
        }
        if ($comfort >= 60) {
            return 'Good; monitor ventilation';
        }
        if ($comfort >= 40) {
            if ($tempC > 26) return 'Warm; increase ventilation, ensure water';
            if ($tempC < 12) return 'Cool; provide bedding/heat lamps for piglets';
            return 'Moderate; check stocking density';
        }
        if ($tempC >= 30) return 'Heat stress risk; cooling and shade needed';
        if ($tempC <= 5) return 'Cold stress risk; enhance heating and bedding';
        if ($rh >= 85) return 'High humidity; boost ventilation to reduce respiratory risk';
        return 'Suboptimal; adjust housing and nutrition';
    }
}
