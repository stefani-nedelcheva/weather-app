<?php

namespace Tests\Feature;

use App\Contracts\WeatherProvider;
use App\Models\Weather;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeatherApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-02-17 12:15:00'));

        $this->app->bind(WeatherProvider::class, function () {
            return new class implements WeatherProvider {
                public function getCurrentTemperature(string $city): int
                {
                    return 4;
                }
            };
        });
    }

    public function test_returns_weather_payload_for_city(): void
    {
        foreach (range(1, 10) as $day) {
            Weather::query()->create([
                'city' => 'Sofia',
                'temperature' => 2,
                'created_at' => now()->subDays($day)->startOfHour(),
            ]);
        }

        $this->getJson('/api/weather?city=Sofia')
            ->assertOk()
            ->assertJson([
                'city' => 'Sofia',
                'temperature' => 4,
                'trend' => ':hot_face:',
                'display' => '4 :hot_face:',
            ]);
    }

    public function test_returns_static_trend_when_average_equals_current(): void
    {
        foreach (range(1, 10) as $day) {
            Weather::query()->create([
                'city' => 'Sofia',
                'temperature' => 4,
                'created_at' => now()->subDays($day)->startOfHour(),
            ]);
        }

        $this->getJson('/api/weather?city=Sofia')
            ->assertOk()
            ->assertJsonPath('trend', '-');
    }

    public function test_validates_missing_city(): void
    {
        $this->getJson('/api/weather')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['city']);
    }
}
