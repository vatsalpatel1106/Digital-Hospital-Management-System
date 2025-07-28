<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DoctorAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle($request, Closure $next)
{
    if (!session()->has('reid') || session('role') !== 'doctor') {
        return redirect('/doctor/login')->withErrors(['login_error' => 'Unauthorized']);
    }

    return $next($request);
}
}
