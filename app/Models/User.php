<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'phone', 'avatar_url', 'bio', 'timezone', 'notification_preferences'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notification_preferences' => 'array',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Get all tasks for the user.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Get all expenses for the user.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Get all notes for the user.
     */
    public function notes(): HasMany
    {
        return $this->hasMany(Note::class);
    }

    /**
     * Get all mood entries for the user.
     */
    public function moods(): HasMany
    {
        return $this->hasMany(Mood::class);
    }

    /**
     * Get all budgets for the user.
     */
    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    /**
     * Get all audit logs for the user.
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    /**
     * Get dashboard statistics with trends and chart data
     */
    public function getDashboardStats()
    {
        // Tasks: Today vs Yesterday
        $completedTasksToday = $this->tasks()
            ->where('status', 'completed')
            ->whereDate('completed_at', today())
            ->count();

        $completedTasksYesterday = $this->tasks()
            ->where('status', 'completed')
            ->whereDate('completed_at', today()->subDay())
            ->count();

        $tasksTrend = $completedTasksYesterday > 0
            ? round((($completedTasksToday - $completedTasksYesterday) / $completedTasksYesterday) * 100)
            : ($completedTasksToday > 0 ? 100 : 0);

        // Expenses: This week vs Last week
        $expensesThisWeek = (float) $this->expenses()
            ->confirmed()
            ->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('amount');

        $expensesLastWeek = (float) $this->expenses()
            ->confirmed()
            ->whereBetween('date', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
            ->sum('amount');

        $expensesTrend = $expensesLastWeek > 0
            ? round((($expensesThisWeek - $expensesLastWeek) / $expensesLastWeek) * 100)
            : ($expensesThisWeek > 0 ? 100 : 0);

        // Charts: 7-day trends
        $task7dayTrend = $this->getTask7DayTrend();
        $expense7dayTrend = $this->getExpense7DayTrend();
        $mood7dayTrend = $this->getMood7DayTrend();
        $expenseByCategoryChart = $this->getExpenseByCategory();

        // Recent items for activity sections
        $recentTasks = $this->tasks()
            ->latest('updated_at')
            ->limit(5)
            ->get(['id', 'title', 'status', 'priority', 'due_date', 'updated_at']);

        $recentExpenses = $this->expenses()
            ->latest('date')
            ->limit(5)
            ->get(['id', 'description', 'amount', 'category', 'date', 'status']);

        $recentNotes = $this->notes()
            ->active()
            ->latest('updated_at')
            ->limit(5)
            ->get(['id', 'title', 'category', 'is_pinned', 'updated_at']);

        $recentMoods = $this->moods()
            ->whereDate('recorded_date', '>=', now()->subDays(6))
            ->orderByDesc('recorded_date')
            ->get([
                'id',
                'mood_level',
                'mood_label',
                'energy_level',
                'stress_level',
                'focus_level',
                'recorded_date',
            ]);

        $latestMood = $recentMoods->first() ?: $this->moods()
            ->orderByDesc('recorded_date')
            ->first([
                'id',
                'mood_level',
                'mood_label',
                'energy_level',
                'stress_level',
                'focus_level',
                'recorded_date',
            ]);

        return [
            'tasks_completed_today' => $completedTasksToday,
            'tasks_trend' => $tasksTrend,
            'total_expenses' => $expensesThisWeek,
            'expenses_trend' => $expensesTrend,
            'recent_notes' => $recentNotes,
            'recent_tasks' => $recentTasks,
            'recent_expenses' => $recentExpenses,
            'mood_summary' => [
                'latest' => $latestMood,
                'week_average' => $recentMoods->isNotEmpty()
                    ? round((float) $recentMoods->avg('mood_level'), 1)
                    : null,
                'entries_count' => $recentMoods->count(),
            ],
            'charts' => [
                'tasks_7day' => $task7dayTrend,
                'expenses_7day' => $expense7dayTrend,
                'mood_7day' => $mood7dayTrend,
                'expenses_by_category' => $expenseByCategoryChart,
            ],
        ];
    }

    /**
     * Get task completion trend for the last 7 days
     */
    private function getTask7DayTrend()
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $count = $this->tasks()
                ->where('status', 'completed')
                ->whereDate('completed_at', $date)
                ->count();
            $data[] = [
                'label' => $date->format('M d'),
                'value' => $count,
            ];
        }
        return $data;
    }

    /**
     * Get expense trend for the last 7 days
     */
    private function getExpense7DayTrend()
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $amount = (float) $this->expenses()
                ->confirmed()
                ->whereDate('date', $date)
                ->sum('amount');
            $data[] = [
                'label' => $date->format('M d'),
                'value' => round($amount, 2),
            ];
        }
        return $data;
    }

    /**
     * Get mood trend for the last 7 days
     */
    private function getMood7DayTrend()
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $avg = $this->moods()
                ->whereDate('recorded_date', $date)
                ->average('mood_level');
            $data[] = [
                'label' => $date->format('M d'),
                'value' => $avg ? round((float) $avg, 1) : null,
            ];
        }
        return $data;
    }

    /**
     * Get expense breakdown by category
     */
    private function getExpenseByCategory()
    {
        $thisMonth = now()->format('Y-m');
        $categories = $this->expenses()
            ->confirmed()
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->groupBy('category')
            ->selectRaw('category, SUM(amount) as total')
            ->orderByDesc('total')
            ->get()
            ->take(6);

        return $categories->map(fn ($cat) => [
            'label' => $cat->category,
            'value' => round((float) $cat->total, 2),
        ]);
    }

    /**
     * Update last login timestamp
     */
    public function recordLogin()
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Check if user is active
     */
    public function isActive()
    {
        return $this->is_active === true;
    }

    /**
     * Deactivate user account
     */
    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Reactivate user account
     */
    public function activate()
    {
        $this->update(['is_active' => true]);
    }
}

