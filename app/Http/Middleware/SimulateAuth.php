<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SimulateAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        // Jangan auto-login di rute autentikasi atau admin agar user bisa login/logout normal
        if ($request->is('login', 'register', 'forgot-password', 'reset-password', 'admin*', 'logout')) {
            return $next($request);
        }

        if (app()->environment('local') && !Auth::check()) {
            // Login as the first user automatically for testing
            // (John Doe, ID = 2 since ID 1 is admin)
            Auth::loginUsingId(2);
        }

        return $next($request);
    }
}
