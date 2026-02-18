<?php

namespace App\Services;

use App\Contracts\WeatherProvider;
use Illuminate\Support\Facades\Http;

class OpenMeteoProvider implements WeatherProvider
{
    public function getCurrentTemperature(string $city): int
    {
        $apiBaseUrl = config('services.open_meteo.base_url');
        $geocodingBaseUrl = config('services.open_meteo.geocoding_base_url');

        $geo = Http::baseUrl($geocodingBaseUrl)
            ->get('/v1/search', [
                'name' => $city,
                'count' => 1,
            ])
            ->throw()
            ->json();

        $location = data_get($geo, 'results.0');

        if (! $location) {
            throw new \Exception("City not found: {$city}");
        }

        $forecast = Http::baseUrl($apiBaseUrl)
            ->get('/v1/forecast', [
                'latitude' => data_get($location, 'latitude'),
                'longitude' => data_get($location, 'longitude'),
                'current' => 'temperature_2m',
            ])
            ->throw()
            ->json();

        $temperature = data_get($forecast, 'current.temperature_2m');

        if (! is_numeric($temperature)) {
            throw new \Exception('Invalid weather provider response.');
        }

        return (int) round($temperature);
    }
}
