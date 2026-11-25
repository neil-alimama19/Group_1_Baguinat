<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WeatherService
{
    /**
     * Get the current weather as an array, or null if nothing is available.
     * Keys: temperature_2m, relative_humidity_2m, wind_speed_10m, weather_code
     */
    public function current(): ?array
    {
        // Reuse the saved result for 30 minutes; otherwise call the API through fetch()
        $weather = Cache::remember('weather.current', now()->addMinutes(30), fn () => $this->fetch());

        // If the API failed (null), fall back to the last good result saved earlier
        return $weather ?? Cache::get('weather.last');
    }

    /**
     * Call the Open-Meteo API. Returns the "current" data, or null on any failure.
     */
    private function fetch(): ?array
    {
        try {
            // Give up after 3 seconds so the dashboard never hangs
            $response = Http::timeout(3)->get(config('services.weather.url'), [
                'latitude'  => config('services.weather.latitude'),   // from config(), not env()
                'longitude' => config('services.weather.longitude'),
                'timezone'  => 'Asia/Manila',
                'current'   => 'temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code',
            ]);
        } catch (ConnectionException $e) {
            // No internet, DNS problem or timeout
            return null;
        }

        // The API answered with an error (4xx or 5xx)
        if ($response->failed()) {
            return null;
        }

        $current = $response->json('current');

        // Make sure we really got the "current" block before using it
        if (! is_array($current)) {
            return null;
        }

        // Keep the last good result for one day, as a backup when the API is down
        Cache::put('weather.last', $current, now()->addDay());

        return $current;
    }
}