<?php

namespace App\Contracts;

interface WeatherProvider
{
    public function getCurrentTemperature(string $city): int;
}
