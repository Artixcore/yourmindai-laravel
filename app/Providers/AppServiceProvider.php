<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Patient;
use App\Models\Session;
use App\Models\SessionDay;
use App\Policies\PatientPolicy;
use App\Policies\SessionPolicy;
use App\Policies\SessionDayPolicy;

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
    }
}
