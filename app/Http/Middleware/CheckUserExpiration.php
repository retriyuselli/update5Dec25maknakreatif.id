<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip checks for Livewire internal requests
        if ($request->is('livewire/*') || $request->header('X-Livewire')) {
            return $next($request);
        }

        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            // Ensure we have the right User model instance
            if (! $user instanceof User) {
                $user = User::find($user->id);
            }

            // Check if user is expired
            if ($user && method_exists($user, 'isExpired') && $user->isExpired()) {
                Auth::logout();

                // Invalidate session
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Redirect with message
                $appUrl = config('app.url') ?: url('/');
                return redirect()->to($appUrl)
                    ->with('error', 'Akun Anda telah kedaluwarsa. Silakan hubungi administrator untuk memperpanjang akses.');
            }

            // Show warning if account will expire soon (only once per session)
            if ($user && method_exists($user, 'isExpiringSoon') && $user->isExpiringSoon()) {
                $sessionKey = 'expiration_warning_shown_'.$user->id;

                // Only show notification if not already shown in this session
                if (! session()->has($sessionKey)) {
                    $days = method_exists($user, 'getDaysUntilExpiration') ? $user->getDaysUntilExpiration() : 0;

                    // Send Filament notification
                    Notification::make()
                        ->warning()
                        ->title('Peringatan: Akun Akan Kedaluwarsa')
                        ->body("Akun Anda akan kedaluwarsa dalam $days hari. Silakan hubungi administrator untuk memperpanjang akses.")
                        ->persistent()
                        ->icon('heroicon-o-exclamation-triangle')
                        ->actions([
                            Action::make('contact')
                                ->label('Hubungi Admin')
                                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                                ->url('https://wa.me/6281373183794?text=Halo,%20saya%20perlu%20bantuan%20untuk%20memperpanjang%20akses%20akun%20saya.')
                                ->openUrlInNewTab(),
                        ])
                        ->send();

                    // Mark as shown in this session
                    session()->put($sessionKey, true);

                    // Also keep session flash for non-Filament pages
                    session()->flash('warning', "Akun Anda akan kedaluwarsa dalam $days hari. Silakan hubungi administrator.");
                }
            }
        }

        return $next($request);
    }
}
