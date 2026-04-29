<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class AccountDeletionRequested extends Notification
{
    use Queueable;

    public function __construct(
        private readonly int $deletionId,
        private readonly string $token,
        private readonly int $minutesValid = 60,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = URL::temporarySignedRoute(
            'account-deletion.confirm',
            now()->addMinutes($this->minutesValid),
            [
                'deletion' => $this->deletionId,
                'token' => $this->token,
            ]
        );

        return (new MailMessage)
            ->subject('Confirm your account deletion')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('A request was made to permanently delete your Digital Life Manager account.')
            ->line('If you initiated this request, click the button below to confirm. This link expires in '.$this->minutesValid.' minutes.')
            ->action('Confirm Account Deletion', $url)
            ->line('If you did not request this, you can safely ignore this email.');
    }
}