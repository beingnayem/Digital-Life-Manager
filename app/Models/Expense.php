<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'amount',
        'category',
        'description',
        'payment_method',
        'date',
        'receipt_url',
        'status',
        'tags',
        'budget_alert_sent',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'budget_alert_sent' => 'boolean',
        'tags' => 'array',
    ];

    /**
     * Get the user that owns the expense.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Get expenses by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: Get expenses in date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereDate('date', '>=', $startDate)
                     ->whereDate('date', '<=', $endDate);
    }

    /**
     * Scope: Get current month expenses
     */
    public function scopeCurrentMonth($query)
    {
        return $query->whereYear('date', now()->year)
                     ->whereMonth('date', now()->month);
    }

    /**
     * Scope: Get expenses by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Get confirmed expenses only
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope: Get high-value expenses
     */
    public function scopeHighValue($query, $threshold = 100)
    {
        return $query->where('amount', '>=', $threshold);
    }

    /**
     * Get monthly summary by category
     */
    public static function monthlySummaryByCategory($userId, $year = null, $month = null)
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        return self::where('user_id', $userId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->groupBy('category')
            ->selectRaw('category, SUM(amount) as total')
            ->get();
    }

    /**
     * Get total expenses for a period
     */
    public static function totalForPeriod($userId, $startDate, $endDate)
    {
        return self::where('user_id', $userId)
            ->betweenDates($startDate, $endDate)
            ->confirmed()
            ->sum('amount');
    }

    /**
     * Check if expense exceeds budget
     */
    public function checkBudgetExceedance()
    {
        $budget = Budget::where('user_id', $this->user_id)
            ->where('category', $this->category)
            ->where('month_year', $this->date->format('Y-m'))
            ->first();

        if (!$budget) {
            return false;
        }

        return $budget->spent_amount >= $budget->limit_amount;
    }

    /**
     * Get percentage of budget used
     */
    public function getBudgetPercentage()
    {
        $budget = Budget::where('user_id', $this->user_id)
            ->where('category', $this->category)
            ->where('month_year', $this->date->format('Y-m'))
            ->first();

        if (!$budget || $budget->limit_amount == 0) {
            return 0;
        }

        return ($budget->spent_amount / $budget->limit_amount) * 100;
    }
}
