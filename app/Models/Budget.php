<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::created(function (Budget $budget): void {
            AuditLog::create([
                'user_id' => auth()->id() ?? $budget->user_id,
                'action' => 'created',
                'entity_type' => 'Budget',
                'entity_id' => $budget->id,
                'old_values' => null,
                'new_values' => $budget->auditSnapshot(),
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
                'created_at' => now(),
            ]);
        });

        static::updating(function (Budget $budget): void {
            $dirty = $budget->getDirty();
            unset($dirty['updated_at']);

            if (empty($dirty)) {
                return;
            }

            $oldValues = [];
            foreach (array_keys($dirty) as $field) {
                $oldValues[$field] = $budget->getOriginal($field);
            }

            AuditLog::create([
                'user_id' => auth()->id() ?? $budget->user_id,
                'action' => 'updated',
                'entity_type' => 'Budget',
                'entity_id' => $budget->id,
                'old_values' => $oldValues,
                'new_values' => $dirty,
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
                'created_at' => now(),
            ]);
        });

        static::deleted(function (Budget $budget): void {
            AuditLog::create([
                'user_id' => auth()->id() ?? $budget->user_id,
                'action' => 'deleted',
                'entity_type' => 'Budget',
                'entity_id' => $budget->id,
                'old_values' => $budget->auditSnapshot(),
                'new_values' => null,
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
                'created_at' => now(),
            ]);
        });
    }

    protected $fillable = [
        'user_id',
        'category',
        'limit_amount',
        'month_year',
        'spent_amount',
        'alert_threshold',
        'is_active',
        'alert_sent_at',
    ];

    protected $casts = [
        'limit_amount' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'alert_sent_at' => 'datetime',
    ];

    /**
     * Get the user that owns the budget.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Get active budgets
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get budgets for a specific month
     */
    public function scopeForMonth($query, $monthYear)
    {
        return $query->where('month_year', $monthYear);
    }

    /**
     * Get budget utilization percentage
     */
    public function getUtilizationPercentage()
    {
        if ($this->limit_amount == 0) {
            return 0;
        }

        return ($this->spent_amount / $this->limit_amount) * 100;
    }

    /**
     * Get remaining budget
     */
    public function getRemaaining()
    {
        return $this->limit_amount - $this->spent_amount;
    }

    /**
     * Check if budget is exceeded
     */
    public function isExceeded()
    {
        return $this->spent_amount >= $this->limit_amount;
    }

    /**
     * Check if budget threshold reached
     */
    public function isThresholdReached()
    {
        $percentage = $this->getUtilizationPercentage();
        return $percentage >= $this->alert_threshold;
    }

    /**
     * Update spent amount from expenses
     */
    public function recalculateSpentAmount()
    {
        $total = Expense::where('user_id', $this->user_id)
            ->where('category', $this->category)
            ->where('status', 'confirmed')
            ->whereYear('date', substr($this->month_year, 0, 4))
            ->whereMonth('date', substr($this->month_year, 5, 2))
            ->sum('amount');

        $this->update(['spent_amount' => $total]);

        return $this;
    }

    /**
     * Refresh totals and send an alert once if the monthly limit is exceeded.
     */
    public function refreshAndAlertIfNeeded(): void
    {
        $this->recalculateSpentAmount();

        if ($this->spent_amount < $this->limit_amount) {
            if ($this->alert_sent_at !== null) {
                $this->forceFill(['alert_sent_at' => null])->saveQuietly();
            }

            return;
        }

        if ($this->alert_sent_at !== null || ! $this->user) {
            return;
        }

        $this->user->notify(new \App\Notifications\BudgetLimitExceededNotification($this));

        $this->forceFill(['alert_sent_at' => now()])->saveQuietly();
    }

    public function searchableAs(): string
    {
        return 'budgets';
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'category' => $this->category,
            'limit_amount' => (float) $this->limit_amount,
            'spent_amount' => (float) $this->spent_amount,
            'alert_threshold' => $this->alert_threshold,
            'is_active' => $this->is_active,
            'month_year' => $this->month_year,
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }

    protected function auditSnapshot(): array
    {
        $data = $this->attributesToArray();

        unset($data['updated_at']);

        return $data;
    }
}
