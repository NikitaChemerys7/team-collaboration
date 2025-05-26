<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    public $token;
    
    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $frontendUrl = 'http://localhost:5173/reset-password/' . $this->token . '?email=' . urlencode($notifiable->getEmailForPasswordReset());

        return (new MailMessage)
            ->subject('Reset password')
            ->greeting('Hello!')
            ->line('You received this email because a password reset request was sent.')
            ->action('Reset Password', $frontendUrl)
            ->line('If you did not request a password reset, just ignore this email.')
            ->salutation('Regards, University Consortium');
    }
}

