<?php

namespace App\Providers;

use App\Models\User;
use App\Services\WeatherService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Fix the giant pagination arrows
        Paginator::useBootstrapFive();

        Gate::define('manage-ssg', function (User $user) {
            return $user->hasRole('Officer');
        });

        Gate::define('view-reports', function (User $user) {
            return $user->hasRole('Officer', 'Adviser');
        });

        Gate::define('use-attendance', function (User $user) {
            return $user->hasRole('Student');
        });

        // View composer: runs only when partials.weather is rendered,
        // so the API/cache is never touched by pages without the widget.
        View::composer('partials.weather', function ($view) {
            try {
                $service = app(WeatherService::class);
                $weather = $service->current(); // array or null

                // Turn the numeric code into text here, so Blade never calls the service
                $label = $weather
                    ? $service->label(data_get($weather, 'weather_code'))
                    : null;
            } catch (\Throwable $e) {
                // Any unexpected problem must not break the dashboard
                report($e);
                $weather = null;
                $label = null;
            }

            $view->with('weather', $weather)->with('weatherLabel', $label);
        });
    }
}