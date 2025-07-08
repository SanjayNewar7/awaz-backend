<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('superadmin_authenticated') || !session('superadmin_authenticated')) {
            return redirect()->route('superadmin.login');
        }

        return $next($request);
    }
}
