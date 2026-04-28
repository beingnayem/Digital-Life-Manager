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
        return [
            'tasks' => [
                'total' => $this->tasks()->count(),
                'completed' => $this->tasks()->where('status', 'completed')->count(),
                'pending' => $this->tasks()->where('status', '!=', 'completed')->count(),
                'overdue' => $this->tasks()
                    ->where('due_date', '<', now())
                    ->where('status', '!=', 'completed')
                    ->count(),
            ],
            'expenses' => [
                'this_month' => $this->expenses()
                    ->currentMonth()
                    ->sum('amount'),
                'average_per_day' => $this->expenses()
                    ->currentMonth()
                    ->average('amount'),
                'by_category' => $this->expenses()
                    ->currentMonth()
                    ->groupBy('category')
                    ->selectRaw('category, SUM(amount) as total')
                    ->get(),
            ],
            'notes' => [
                'total' => $this->notes()->count(),
                'pinned' => $this->notes()->where('is_pinned', true)->count(),
                'archived' => $this->notes()->where('is_archived', true)->count(),
            ],
            'mood' => [
                'today' => $this->moods()
                    ->whereDate('recorded_date', now())
                    ->first(),
                'week_average' => $this->moods()
                    ->where('recorded_date', '>=', now()->subDays(7))
                    ->average('mood_level'),
                'month_average' => $this->moods()
                    ->where('recorded_date', '>=', now()->subMonth())
                    ->average('mood_level'),
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

