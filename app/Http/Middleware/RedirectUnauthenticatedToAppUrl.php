<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectUnauthenticatedToAppUrl
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            $appUrl = config('app.url') ?: url('/');
            return redirect()->to($appUrl);
        }

        return $next($request);
    }
}
