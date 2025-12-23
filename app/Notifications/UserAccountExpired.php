<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserAccountExpired extends Notification
{
    use Queueable;

    protected $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Akun Anda Telah Kedaluwarsa')
            ->greeting('Halo '.$this->user->name.'!')
            ->line('Akun Anda telah kedaluwarsa pada '.$this->user->expire_date->format('d/m/Y H:i').'.')
            ->line('Anda tidak dapat lagi mengakses sistem hingga akun diperpanjang.')
            ->line('Silakan hubungi administrator segera untuk memperpanjang akses Anda.')
            ->line('📱 WhatsApp Admin: 081373183794')
            ->action('Hubungi via WhatsApp', 'https://wa.me/6281373183794?text=Halo,%20akun%20saya%20telah%20kedaluwarsa%20dan%20saya%20perlu%20bantuan%20untuk%20memperpanjangnya.')
            ->line('Terima kasih atas pengertian Anda.')
            ->salutation('Tim Support Makna Online');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Akun Anda telah kedaluwarsa',
            'expire_date' => $this->user->expire_date,
            'expired_at' => now(),
        ];
    }
}
