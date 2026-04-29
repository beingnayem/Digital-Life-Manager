<?php

namespace App\Console\Commands;

use App\Models\Budget;
use Illuminate\Console\Command;

class SendBudgetLimitAlerts extends Command
{
    protected $signature = 'budgets:send-limit-alerts';

    protected $description = 'Send monthly budget alert emails when expenses exceed the budget limit.';

    public function handle(): int
    {
        $budgets = Budget::query()
            ->with('user')
            ->active()
            ->where('month_year', now()->format('Y-m'))
            ->get();

        if ($budgets->isEmpty()) {
            $this->info('No active monthly budgets found.');

            return self::SUCCESS;
        }

        foreach ($budgets as $budget) {
            $previousAlertState = $budget->alert_sent_at;

            $budget->refreshAndAlertIfNeeded();

            if ($previousAlertState === null && $budget->alert_sent_at !== null && $budget->user) {
                $this->line(sprintf(
                    'Sent budget alert to %s for %s (%s/%s).',
                    $budget->user->email,
                    ucfirst($budget->category),
                    number_format((float) $budget->spent_amount, 2),
                    number_format((float) $budget->limit_amount, 2),
                ));
            }
        }

        $this->info('Budget limit alert sweep completed.');

        return self::SUCCESS;
    }
}