<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Notifications\TaskReminderNotification;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class SendTaskReminderEmails extends Command
{
    protected $signature = 'tasks:send-reminders {--date= : Optional Y-m-d date to check instead of tomorrow}';

    protected $description = 'Send reminder emails for tasks due one day before the deadline.';

    public function handle(): int
    {
        $targetDate = $this->option('date')
            ? CarbonImmutable::parse($this->option('date'))->startOfDay()
            : CarbonImmutable::tomorrow()->startOfDay();

        $tasksByUser = Task::query()
            ->with('user')
            ->incomplete()
            ->whereNull('reminder_sent_at')
            ->whereDate('due_date', $targetDate->toDateString())
            ->get()
            ->groupBy('user_id');

        if ($tasksByUser->isEmpty()) {
            $this->info('No task reminders to send for '.$targetDate->toDateString().'.');

            return self::SUCCESS;
        }

        $sentUsers = 0;

        foreach ($tasksByUser as $userId => $tasks) {
            $user = $tasks->first()?->user;

            if (! $user) {
                continue;
            }

            $user->notify(new TaskReminderNotification($tasks, $targetDate));

            // Mark each task as having been reminded to avoid duplicate emails
            foreach ($tasks as $task) {
                $task->update(['reminder_sent_at' => now()]);
            }

            $sentUsers++;
            $this->line("Sent reminder to {$user->email} for {$tasks->count()} task(s).");
        }

        $this->info("Task reminder emails sent to {$sentUsers} user(s).");

        return self::SUCCESS;
    }
}