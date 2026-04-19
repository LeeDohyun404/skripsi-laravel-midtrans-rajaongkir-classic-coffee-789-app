<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class StaffResetPasswordNotification extends Notification
{
    public function __construct(public string $resetUrl) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Reset Password - Staff')
            ->line('Klik tombol di bawah untuk reset password akun staff kamu.')
            ->action('Reset Password', $this->resetUrl)
            ->line('Link ini akan kadaluarsa dalam 60 menit.');
    }
}