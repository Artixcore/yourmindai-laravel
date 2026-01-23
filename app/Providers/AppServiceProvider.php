<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use App\Models\Patient;
use App\Models\Session;
use App\Models\SessionDay;
use App\Models\PatientResource;
use App\Models\AppointmentRequest;
use App\Policies\PatientPolicy;
use App\Policies\SessionPolicy;
use App\Policies\SessionDayPolicy;
use App\Policies\PatientResourcePolicy;
use App\Policies\AppointmentRequestPolicy;

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
        Gate::policy(AppointmentRequest::class, AppointmentRequestPolicy::class);

        // Force HTTPS when:
        // 1. In production environment
        // 2. APP_FORCE_HTTPS is set to true
        // 3. Request is behind proxy with X-Forwarded-Proto header
        // 4. Request is behind proxy with X-Forwarded-Ssl header
        // 5. Request is actually secure
        $forceHttps = false;
        
        if (config('app.env') === 'production' || env('APP_FORCE_HTTPS', false)) {
            $forceHttps = true;
        } elseif (request()->header('X-Forwarded-Proto') === 'https') {
            $forceHttps = true;
        } elseif (request()->header('X-Forwarded-Ssl') === 'on') {
            $forceHttps = true;
        } elseif (request()->secure()) {
            $forceHttps = true;
        }
        
        if ($forceHttps) {
            URL::forceScheme('https');
        }
    }
}
