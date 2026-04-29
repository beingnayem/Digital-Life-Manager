<?php

namespace App\Notifications;

use App\Models\Budget;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BudgetLimitExceededNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Budget $budget)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $utilization = $this->budget->getUtilizationPercentage();

        return (new MailMessage)
            ->subject('Monthly budget alert')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('Your monthly budget for '.ucfirst($this->budget->category).' has been exceeded.')
            ->line('Budget limit: $'.number_format((float) $this->budget->limit_amount, 2))
            ->line('Current monthly expense: $'.number_format((float) $this->budget->spent_amount, 2))
            ->line('Utilization: '.number_format($utilization, 1).'%')
            ->line('Please review your spending to stay on track for this month.');
    }
}