<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use App\Models\Patient;
use App\Models\Session;
use App\Models\SessionDay;
use App\Models\PatientResource;
use App\Policies\PatientPolicy;
use App\Policies\SessionPolicy;
use App\Policies\SessionDayPolicy;
use App\Policies\PatientResourcePolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Patient::class, PatientPolicy::class);
        Gate::policy(Session::class, SessionPolicy::class);
        Gate::policy(SessionDay::class, SessionDayPolicy::class);
        Gate::policy(PatientResource::class, PatientResourcePolicy::class);

        // Force HTTPS in production or when APP_FORCE_HTTPS is set
        if (config('app.env') === 'production' || env('APP_FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }
    }
}
