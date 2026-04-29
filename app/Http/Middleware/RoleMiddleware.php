<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        // 1. Check if the user is even logged in
        if (!Auth::check()) {
            return redirect('/login');
        }

        // 2. Check if the logged-in user has the required role
        if (Auth::user()->role !== $role) {
            // If they aren't an admin trying to see admin pages, kick them out
            return redirect('/')->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}