{{-- Weather widget. $weather and $weatherLabel come from the View composer in AppServiceProvider --}}
<div class="card">
    <h2>Weather Today</h2>

    @if ($weather)
        <div class="stats-grid">
            <div class="stat-box">
                <h3>Condition</h3>
                {{-- Text label made by WeatherService::label() --}}
                <div class="number" style="font-size: 20px;">{{ $weatherLabel }}</div>
            </div>
            <div class="stat-box">
                <h3>Temperature</h3>
                {{-- data_get() safely reads a key, with '—' as the default --}}
                <div class="number">{{ data_get($weather, 'temperature_2m', '—') }}°C</div>
            </div>
            <div class="stat-box">
                <h3>Humidity</h3>
                <div class="number">{{ data_get($weather, 'relative_humidity_2m', '—') }}%</div>
            </div>
            <div class="stat-box">
                <h3>Wind</h3>
                <div class="number">{{ data_get($weather, 'wind_speed_10m', '—') }} km/h</div>
            </div>
        </div>
    @else
        {{-- Shown when the API and the saved backup are both unavailable --}}
        <p>Weather is currently unavailable</p>
    @endif

    <p style="font-size: 12px; margin-top: 10px; opacity: 0.8;">
        <a href="https://open-meteo.com/" target="_blank" rel="noopener">Weather data by Open-Meteo</a>
    </p>
</div>