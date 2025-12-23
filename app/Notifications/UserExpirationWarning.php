<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserExpirationWarning extends Notification
{
    use Queueable;

    protected $user;

    protected $daysUntilExpiration;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, int $daysUntilExpiration)
    {
        $this->user = $user;
        $this->daysUntilExpiration = $daysUntilExpiration;
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
        $urgencyLevel = $this->getUrgencyLevel();
        $subject = $this->getSubjectByUrgency();

        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->greeting('Halo '.$this->user->name.'!');

        // Customize message based on urgency
        if ($this->daysUntilExpiration <= 0) {
            $mailMessage->line('⚠️ **URGENT**: Akun Anda telah kedaluwarsa!')
                ->line('Tanggal kedaluwarsa: '.$this->user->expire_date->format('d/m/Y H:i'))
                ->line('Anda tidak dapat lagi mengakses sistem hingga akun diperpanjang.');
        } elseif ($this->daysUntilExpiration == 1) {
            $mailMessage->line('🚨 **KRITIKAL**: Akun Anda akan kedaluwarsa BESOK!')
                ->line('Tanggal kedaluwarsa: '.$this->user->expire_date->format('d/m/Y H:i'))
                ->line('Segera hubungi administrator untuk memperpanjang akses Anda.');
        } elseif ($this->daysUntilExpiration <= 3) {
            $mailMessage->line('⚠️ **MENDESAK**: Akun Anda akan kedaluwarsa dalam '.$this->daysUntilExpiration.' hari.')
                ->line('Tanggal kedaluwarsa: '.$this->user->expire_date->format('d/m/Y H:i'))
                ->line('Harap segera hubungi administrator untuk memperpanjang akses Anda.');
        } elseif ($this->daysUntilExpiration <= 7) {
            $mailMessage->line('⚠️ **PERINGATAN**: Akun Anda akan kedaluwarsa dalam '.$this->daysUntilExpiration.' hari.')
                ->line('Tanggal kedaluwarsa: '.$this->user->expire_date->format('d/m/Y H:i'))
                ->line('Silakan hubungi administrator untuk memperpanjang akses Anda.');
        } else {
            $mailMessage->line('� **PEMBERITAHUAN**: Akun Anda akan kedaluwarsa dalam '.$this->daysUntilExpiration.' hari.')
                ->line('Tanggal kedaluwarsa: '.$this->user->expire_date->format('d/m/Y H:i'))
                ->line('Silakan merencanakan untuk memperpanjang akses Anda.');
        }

        $mailMessage->line('')
            ->line('📱 **Kontak Administrator:**')
            ->line('WhatsApp: 081373183794');

        // Add actions based on status
        if ($this->daysUntilExpiration > 0) {
            $mailMessage->action('Login ke Sistem', url('/admin'));
        }

        $mailMessage->action('Hubungi via WhatsApp', 'https://wa.me/6281373183794?text=Halo,%20saya%20perlu%20bantuan%20untuk%20memperpanjang%20akses%20akun%20saya.%20Nama:%20'.urlencode($this->user->name).'%20Email:%20'.urlencode($this->user->email))
            ->line('')
            ->line('Terima kasih telah menggunakan aplikasi kami!')
            ->salutation('Tim Support Makna Online');

        return $mailMessage;
    }

    /**
     * Get urgency level based on days until expiration
     */
    private function getUrgencyLevel(): string
    {
        if ($this->daysUntilExpiration <= 0) {
            return 'expired';
        } elseif ($this->daysUntilExpiration == 1) {
            return 'critical';
        } elseif ($this->daysUntilExpiration <= 3) {
            return 'urgent';
        } elseif ($this->daysUntilExpiration <= 7) {
            return 'warning';
        } else {
            return 'notice';
        }
    }

    /**
     * Get email subject based on urgency
     */
    private function getSubjectByUrgency(): string
    {
        $urgency = $this->getUrgencyLevel();

        switch ($urgency) {
            case 'expired':
                return '🚨 URGENT: Akun Anda Telah Kedaluwarsa';
            case 'critical':
                return '🚨 KRITIKAL: Akun Kedaluwarsa Besok!';
            case 'urgent':
                return '⚠️ MENDESAK: Akun Kedaluwarsa dalam '.$this->daysUntilExpiration.' Hari';
            case 'warning':
                return '⚠️ PERINGATAN: Akun Kedaluwarsa dalam '.$this->daysUntilExpiration.' Hari';
            default:
                return '📋 PEMBERITAHUAN: Akun Kedaluwarsa dalam '.$this->daysUntilExpiration.' Hari';
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $urgencyLevel = $this->getUrgencyLevel();

        return [
            'message' => $this->daysUntilExpiration <= 0
                ? 'Akun Anda telah kedaluwarsa'
                : 'Akun Anda akan kedaluwarsa dalam '.$this->daysUntilExpiration.' hari',
            'expire_date' => $this->user->expire_date,
            'days_until_expiration' => $this->daysUntilExpiration,
            'urgency_level' => $urgencyLevel,
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
            'whatsapp_contact' => '081373183794',
            'sent_at' => now(),
        ];
    }
}
