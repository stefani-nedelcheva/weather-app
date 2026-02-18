<?php

namespace App\Repositories;

use App\Models\Weather;
use Carbon\CarbonInterface;

class WeatherRepository
{
    public function averageLast10Days(string $city, CarbonInterface $untilExclusive): ?float
    {
        $average = Weather::query()
            ->where('city', $city)
            ->where('created_at', '>=', $untilExclusive->copy()->subDays(10))
            ->where('created_at', '<', $untilExclusive)
            ->avg('temperature');

        return $average === null ? null : (float) $average;
    }

    public function storeHourly(string $city, int $temperature, CarbonInterface $recordedAt): void
    {
        Weather::query()->updateOrCreate(
            [
                'city' => $city,
                'created_at' => $recordedAt,
            ],
            [
                'temperature' => $temperature,
            ]
        );
    }
}
