<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckProjectAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('filament.admin.auth.login');
        }

        $user = Auth::user();

        // Allowed roles for project access
        $allowedRoles = ['super_admin', 'Account Manager', 'Finance'];

        // Check if user has any of the allowed roles
        $hasAccess = false;

        // Check if user has hasRole method (Spatie Permission)
        if (method_exists($user, 'hasRole')) {
            foreach ($allowedRoles as $role) {
                if ($user->hasRole($role)) {
                    $hasAccess = true;
                    break;
                }
            }
        } else {
            // Fallback: check user's role field directly
            if (in_array($user->role, $allowedRoles)) {
                $hasAccess = true;
            }
        }

        if (! $hasAccess) {
            // Redirect with error message
            return redirect()->route('filament.admin.pages.dashboard')
                ->with('error', 'Anda tidak memiliki akses ke halaman Project Management. Hanya Super Admin, Account Manager, dan Finance yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}
