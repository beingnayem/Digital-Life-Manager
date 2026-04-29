<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Task extends Model
{
    use HasFactory, SoftDeletes, Searchable;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'priority',
        'status',
        'due_date',
        'reminder_sent_at',
        'completed_at',
        'estimated_hours',
        'actual_hours',
        'color_tag',
        'is_recurring',
        'recurrence_pattern',
        'tags',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_recurring' => 'boolean',
        'tags' => 'array',
    ];

    /**
     * Get the user that owns the task.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Get only incomplete tasks
     */
    public function scopeIncomplete($query)
    {
        return $query->where('status', '!=', 'completed')
                     ->where('status', '!=', 'archived')
                     ->where('status', '!=', 'cancelled');
    }

    /**
     * Scope: Get tasks by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Get tasks by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope: Get tasks due today or overdue
     */
    public function scopeDueToday($query)
    {
        return $query->whereDate('due_date', '<=', now())
                     ->where('status', '!=', 'completed');
    }

    /**
     * Scope: Get tasks due this week
     */
    public function scopeDueThisWeek($query)
    {
        return $query->whereDate('due_date', '>=', now())
                     ->whereDate('due_date', '<', now()->addWeek())
                     ->where('status', '!=', 'completed');
    }

    /**
     * Check if task is overdue
     */
    public function isOverdue()
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'completed';
    }

    /**
     * Mark task as completed
     */
    public function complete()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Calculate time remaining
     */
    public function hoursRemaining()
    {
        if (!$this->estimated_hours) {
            return null;
        }

        return max(0, $this->estimated_hours - ($this->actual_hours ?? 0));
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentage()
    {
        if (!$this->estimated_hours) {
            return $this->status === 'completed' ? 100 : 0;
        }

        return min(100, (($this->actual_hours ?? 0) / $this->estimated_hours) * 100);
    }

    public function searchableAs(): string
    {
        return 'tasks';
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'priority' => $this->priority,
            'status' => $this->status,
            'due_date' => optional($this->due_date)->toDateString(),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
