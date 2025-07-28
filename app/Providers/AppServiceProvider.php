<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\PatientAuth;
use App\Http\Middleware\DoctorAuth;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Route::middleware('web')
            ->middleware([
                'patient.auth' => PatientAuth::class,
                'doctor.auth' => DoctorAuth::class,
            ])
            ->group(base_path('routes/web.php'));

        
    }
}