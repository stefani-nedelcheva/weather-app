<?php

namespace Tests\Unit;

use App\Services\WeatherTrend;
use PHPUnit\Framework\TestCase;

class WeatherTrendTest extends TestCase
{
    public function test_returns_static_when_no_average_exists(): void
    {
        $this->assertSame('-', (new WeatherTrend())->getTrend(4, null));
    }

    public function test_returns_hot_face_when_current_is_above_average(): void
    {
        $this->assertSame(':hot_face:', (new WeatherTrend())->getTrend(4, 2.0));
    }

    public function test_returns_cold_face_when_current_is_below_average(): void
    {
        $this->assertSame(':cold_face:', (new WeatherTrend())->getTrend(2, 4.0));
    }

    public function test_returns_static_when_equal_to_average(): void
    {
        $this->assertSame('-', (new WeatherTrend())->getTrend(4, 4.0));
    }
}
