<?php

namespace Tests\Feature;

use App\Contracts\WeatherProvider;
use App\Models\Weather;
use App\Services\WeatherService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class WeatherCachingTest extends TestCase
{
    use RefreshDatabase;

    public function test_calls_provider_once_for_repeated_city_within_one_hour(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-02-17 12:15:00'));
        Cache::flush();

        $provider = new class implements WeatherProvider {
            public int $calls = 0;

            public function getCurrentTemperature(string $city): int
            {
                $this->calls++;
                return 4;
            }
        };

        app()->instance(WeatherProvider::class, $provider);

        $service = app(WeatherService::class);

        $first = $service->getByCity('Sofia');
        $second = $service->getByCity('Sofia');

        $this->assertSame(4, $first['temperature']);
        $this->assertSame(4, $second['temperature']);
        $this->assertSame(1, $provider->calls);
        $this->assertSame(1, Weather::query()->where('city', 'Sofia')->count());
    }
}
