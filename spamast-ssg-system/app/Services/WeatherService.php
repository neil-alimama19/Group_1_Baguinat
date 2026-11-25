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
     * Convert an Open-Meteo weather_code (WMO code) into short text.
     */
    public function label(?int $code): string
    {
        return match (true) {
            $code === null                   => 'Unknown',
            $code === 0                      => 'Clear',
            in_array($code, [1, 2, 3])       => 'Partly Cloudy',   // mainly clear, partly cloudy, overcast
            in_array($code, [45, 48])        => 'Fog',
            $code >= 51 && $code <= 67       => 'Rain',            // drizzle and rain
            $code >= 80 && $code <= 82       => 'Rain Showers',
            $code >= 95 && $code <= 99       => 'Thunderstorm',
            default                          => 'Unknown',         // any code not in your list
        };
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