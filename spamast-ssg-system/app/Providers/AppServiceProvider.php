<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator; // Add this line
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    // ... register() method ...

    public function boot(): void
    {
        // Add this line to fix the giant arrows!
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
    }
}