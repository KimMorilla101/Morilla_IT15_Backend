<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'city' => ['nullable', 'string', 'max:100'],
            'lat'  => ['nullable', 'numeric', 'between:-90,90'],
            'lon'  => ['nullable', 'numeric', 'between:-180,180'],
            'days' => ['nullable', 'integer', 'between:1,5'],
        ]);

        $query = $validated['city']
            ?? (isset($validated['lat'], $validated['lon'])
                ? $validated['lat'].','.$validated['lon']
                : 'Davao');

        $days     = (int) ($validated['days'] ?? 5);
        $cacheKey = 'weather_'.md5($query.'_'.$days);
        $cacheTtl = (int) config('services.weather.cache_ttl', 10) * 60;

        $cached = Cache::get($cacheKey);

        $apiKey = (string) config('services.weather.key');

        if ($apiKey === '') {
            if ($cached !== null) {
                return response()->json($cached + ['meta' => ['source' => 'cache', 'warning' => 'Weather API key not configured. Returning cached data.']]);
            }

            return response()->json([
                'message' => 'Weather API key is not configured. Set WEATHER_API_KEY in your .env file.',
            ], 503);
        }

        $response = Http::timeout(8)->get('https://api.weatherapi.com/v1/forecast.json', [
            'key'     => $apiKey,
            'q'       => $query,
            'days'    => $days,
            'aqi'     => 'no',
            'alerts'  => 'no',
        ]);

        if ($response->failed()) {
            if ($cached !== null) {
                return response()->json($cached + [
                    'meta' => [
                        'source'  => 'stale_cache',
                        'warning' => 'Live weather unavailable. Returning cached data.',
                    ],
                ]);
            }

            $errorMessage = $response->json('error.message', 'Unable to fetch weather data.');

            return response()->json(['message' => $errorMessage], $response->status() ?: 502);
        }

        $data = $this->formatResponse($response->json(), $days);

        Cache::put($cacheKey, $data, $cacheTtl);

        return response()->json($data + ['meta' => ['source' => 'api']]);
    }

    /**
     * @param  array<string, mixed>  $raw
     * @return array<string, mixed>
     */
    private function formatResponse(array $raw, int $days): array
    {
        $current  = $raw['current'] ?? [];
        $location = $raw['location'] ?? [];
        $forecast = $raw['forecast']['forecastday'] ?? [];

        return [
            'location' => [
                'city'       => $location['name'] ?? null,
                'region'     => $location['region'] ?? null,
                'country'    => $location['country'] ?? null,
                'local_time' => $location['localtime'] ?? null,
            ],
            'current' => [
                'temperature_c' => $current['temp_c'] ?? null,
                'temperature_f' => $current['temp_f'] ?? null,
                'feels_like_c'  => $current['feelslike_c'] ?? null,
                'humidity'      => $current['humidity'] ?? null,
                'wind_kph'      => $current['wind_kph'] ?? null,
                'wind_dir'      => $current['wind_dir'] ?? null,
                'condition'     => $current['condition']['text'] ?? null,
                'icon'          => isset($current['condition']['icon'])
                    ? 'https:'.$current['condition']['icon']
                    : null,
                'uv_index'      => $current['uv'] ?? null,
            ],
            'forecast' => collect($forecast)
                ->take($days)
                ->map(fn (array $day): array => [
                    'date'              => $day['date'] ?? null,
                    'max_temp_c'        => $day['day']['maxtemp_c'] ?? null,
                    'min_temp_c'        => $day['day']['mintemp_c'] ?? null,
                    'avg_temp_c'        => $day['day']['avgtemp_c'] ?? null,
                    'humidity'          => $day['day']['avghumidity'] ?? null,
                    'condition'         => $day['day']['condition']['text'] ?? null,
                    'icon'              => isset($day['day']['condition']['icon'])
                        ? 'https:'.$day['day']['condition']['icon']
                        : null,
                    'chance_of_rain'    => $day['day']['daily_chance_of_rain'] ?? null,
                ])
                ->values()
                ->all(),
        ];
    }
}
