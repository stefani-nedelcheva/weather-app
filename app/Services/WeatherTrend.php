<?php

namespace App\Services;

class WeatherTrend
{
    public function getTrend(int $temperature, ?float $average): string
    {
        if ($average === null) {
            return '-';
        }

        if ($temperature > $average) {
            return ':hot_face:';
        }

        if ($temperature < $average) {
            return ':cold_face:';
        }

        return '-';
    }
}
