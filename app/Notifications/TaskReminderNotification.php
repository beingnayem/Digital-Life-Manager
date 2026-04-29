<?php

namespace App\Notifications;

use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class TaskReminderNotification extends Notification
{
    use Queueable;

    /**
     * @param  \Illuminate\Support\Collection<int, \App\Models\Task>  $tasks
     */
    public function __construct(
        private readonly Collection $tasks,
        private readonly CarbonInterface $dueDate,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Task reminder: due tomorrow')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('You have '. $this->tasks->count().' task(s) due on '.$this->dueDate->format('M d, Y').'.')
            ->line('Here is your upcoming workload:');

        foreach ($this->tasks as $task) {
            $message->line('• '.$task->title.' (Priority: '.ucfirst((string) $task->priority).')');
        }

        return $message
            ->line('Open Digital Life Manager to review and complete them before the deadline.');
    }
}