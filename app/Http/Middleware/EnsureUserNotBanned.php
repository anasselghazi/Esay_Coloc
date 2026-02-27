<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserNotBanned
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->status === 'banned') {
            Auth::logout();
            return redirect()->route('login')->withErrors(['Votre compte est banni.']);
        }

        return $next($request);
    }
}
