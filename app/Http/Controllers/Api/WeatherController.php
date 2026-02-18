<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function __invoke(Request $request, WeatherService $weatherService): JsonResponse
    {
        $validated = $request->validate([
            'city' => ['required', 'string', 'max:120'],
        ]);

        try {
            return response()->json($weatherService->getByCity($validated['city']));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
