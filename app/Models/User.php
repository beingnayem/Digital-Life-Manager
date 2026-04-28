<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'phone', 'avatar_url', 'bio', 'timezone', 'notification_preferences'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
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
     * Get dashboard statistics
     */
    public function getDashboardStats()
    {
        $completedTasksToday = $this->tasks()
            ->where('status', 'completed')
            ->whereDate('completed_at', today())
            ->count();

        $totalExpenses = (float) $this->expenses()
            ->confirmed()
            ->sum('amount');

        $recentNotes = $this->notes()
            ->active()
            ->latest('updated_at')
            ->limit(5)
            ->get([
                'id',
                'title',
                'category',
                'is_pinned',
                'updated_at',
            ]);

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
            'total_expenses' => $totalExpenses,
            'recent_notes' => $recentNotes,
            'mood_summary' => [
                'latest' => $latestMood,
                'week_average' => $recentMoods->isNotEmpty()
                    ? round((float) $recentMoods->avg('mood_level'), 1)
                    : null,
                'entries_count' => $recentMoods->count(),
            ],
        ];
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

