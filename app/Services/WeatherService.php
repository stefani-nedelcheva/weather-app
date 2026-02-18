<?php

namespace App\Services;

use App\Contracts\WeatherProvider;
use App\Repositories\WeatherRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class WeatherService
{
    public function __construct(
        private readonly WeatherProvider $provider,
        private readonly WeatherRepository $weatherRepository,
        private readonly WeatherTrend $weatherTrend
    ) {
    }

    public function getByCity(string $city): array
    {
        $city = $this->normalizeCity($city);
        $comparisonPoint = now()->startOfHour();

        $average = $this->weatherRepository->averageLast10Days($city, $comparisonPoint);
        $payload = $this->getData($city, $comparisonPoint);

        $temperature = (int) $payload['temperature'];
        $trend = $this->weatherTrend->getTrend($temperature, $average);

        return [
            'city' => $city,
            'temperature' => $temperature,
            'trend' => $trend,
            'display' => "{$temperature} {$trend}",
        ];
    }

    private function getData(string $city, $recordedAt): array
    {
        $cacheKey = 'weather:city:' . Str::lower($city);
        $lockKey = 'lock:' . $cacheKey;

        $cached = Cache::get($cacheKey);
        if (is_array($cached)) {
            return $cached;
        }

        $result = null;

        Cache::lock($lockKey, 10)->block(3, function () use ($cacheKey, $city, $recordedAt, &$result): void {
            $cachedInside = Cache::get($cacheKey);
            if (is_array($cachedInside)) {
                $result = $cachedInside;
                return;
            }

            $temperature = $this->provider->getCurrentTemperature($city);

            $this->weatherRepository->storeHourly($city, $temperature, $recordedAt);

            $result = [
                'temperature' => $temperature,
                'created_at' => $recordedAt->toIso8601String(),
            ];

            Cache::put($cacheKey, $result, now()->addHour());
        });

        return $result;
    }

    private function normalizeCity(string $city): string
    {
        return Str::of($city)->trim()->squish()->title()->value();
    }
}
