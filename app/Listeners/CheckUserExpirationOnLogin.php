<?php

namespace App\Listeners;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Auth\Events\Login;

class CheckUserExpirationOnLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        /** @var User $user */
        $user = $event->user;

        if (! $user instanceof User) {
            return;
        }

        // Check if user is expiring soon and hasn't received daily login notification
        if (method_exists($user, 'isExpiringSoon') && $user->isExpiringSoon()) {
            $sessionKey = 'daily_login_warning_'.$user->id.'_'.now()->format('Y-m-d');

            // Only send welcome notification once per day
            if (! session()->has($sessionKey)) {
                $days = method_exists($user, 'getDaysUntilExpiration') ? $user->getDaysUntilExpiration() : 0;

                $this->sendWelcomeExpirationNotification($user, $days);

                // Mark as sent for today
                session()->put($sessionKey, true);
            }
        }
    }

    protected function sendWelcomeExpirationNotification(User $user, int $days): void
    {
        $color = match (true) {
            $days <= 1 => 'danger',
            $days <= 3 => 'warning',
            default => 'info'
        };

        $icon = match (true) {
            $days <= 1 => 'heroicon-o-exclamation-circle',
            $days <= 3 => 'heroicon-o-exclamation-triangle',
            default => 'heroicon-o-information-circle'
        };

        Notification::make()
            ->title("Selamat datang, {$user->name}!")
            ->body("Akun Anda akan kedaluwarsa dalam $days hari. Silakan hubungi administrator untuk memperpanjang akses.")
            ->color($color)
            ->icon($icon)
            ->duration(10000) // 10 seconds
            ->actions([
                Action::make('contact')
                    ->label('Hubungi Admin')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->url('https://wa.me/6281373183794?text=Halo,%20saya%20perlu%20bantuan%20untuk%20memperpanjang%20akses%20akun%20saya.')
                    ->openUrlInNewTab(),
                Action::make('view_details')
                    ->label('Masuk')
                    ->icon('heroicon-o-eye')
                    ->url('/admin/users/'.$user->id)
                    ->openUrlInNewTab(),
            ])
            ->send();
    }
}
