<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category',
        'limit_amount',
        'month_year',
        'spent_amount',
        'alert_threshold',
        'is_active',
    ];

    protected $casts = [
        'limit_amount' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'is_active' => 'boolean',
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
    }
}
