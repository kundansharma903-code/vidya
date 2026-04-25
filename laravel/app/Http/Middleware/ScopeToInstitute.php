<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ScopeToInstitute
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user      = Auth::user();
            $institute = $user->institute;

            View::share('currentInstitute', $institute);
            View::share('instituteName', $institute?->name ?? 'Vidya');
            View::share('instituteLogo', $institute?->logo_path ? asset('storage/'.$institute->logo_path) : null);
        }

        return $next($request);
    }
}
