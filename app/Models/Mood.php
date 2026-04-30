<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mood extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::created(function (Mood $mood): void {
            AuditLog::create([
                'user_id' => auth()->id() ?? $mood->user_id,
                'action' => 'created',
                'entity_type' => 'Mood',
                'entity_id' => $mood->id,
                'old_values' => null,
                'new_values' => $mood->auditSnapshot(),
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
                'created_at' => now(),
            ]);
        });

        static::updating(function (Mood $mood): void {
            $dirty = $mood->getDirty();
            unset($dirty['updated_at']);

            if (empty($dirty)) {
                return;
            }

            $oldValues = [];
            foreach (array_keys($dirty) as $field) {
                $oldValues[$field] = $mood->getOriginal($field);
            }

            AuditLog::create([
                'user_id' => auth()->id() ?? $mood->user_id,
                'action' => 'updated',
                'entity_type' => 'Mood',
                'entity_id' => $mood->id,
                'old_values' => $oldValues,
                'new_values' => $dirty,
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
                'created_at' => now(),
            ]);
        });

        static::deleted(function (Mood $mood): void {
            AuditLog::create([
                'user_id' => auth()->id() ?? $mood->user_id,
                'action' => 'deleted',
                'entity_type' => 'Mood',
                'entity_id' => $mood->id,
                'old_values' => $mood->auditSnapshot(),
                'new_values' => null,
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
                'created_at' => now(),
            ]);
        });
    }

    protected $fillable = [
        'user_id',
        'mood_level',
        'mood_label',
        'energy_level',
        'stress_level',
        'focus_level',
        'emotion_tags',
        'notes',
        'activities',
        'sleep_hours',
        'weather',
        'location',
        'recorded_date',
        'recorded_at',
    ];

    protected $casts = [
        'recorded_date' => 'date',
        'recorded_at' => 'datetime',
        'emotion_tags' => 'array',
        'activities' => 'array',
        'sleep_hours' => 'float',
    ];

    /**
     * Get the user that owns the mood.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Get mood entries for a date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereDate('recorded_date', '>=', $startDate)
                     ->whereDate('recorded_date', '<=', $endDate);
    }

    /**
     * Scope: Get mood entries for a specific month
     */
    public function scopeForMonth($query, $year = null, $month = null)
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        return $query->whereYear('recorded_date', $year)
                     ->whereMonth('recorded_date', $month);
    }

    /**
     * Scope: Get mood entries for a specific day
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('recorded_date', $date);
    }

    /**
     * Scope: Get positive moods (8-10)
     */
    public function scopePositive($query)
    {
        return $query->where('mood_level', '>=', 8);
    }

    /**
     * Scope: Get negative moods (1-4)
     */
    public function scopeNegative($query)
    {
        return $query->where('mood_level', '<=', 4);
    }

    /**
     * Scope: Get high stress entries
     */
    public function scopeHighStress($query)
    {
        return $query->where('stress_level', '>=', 7);
    }

    /**
     * Scope: Get low energy entries
     */
    public function scopeLowEnergy($query)
    {
        return $query->where('energy_level', '<=', 3);
    }

    /**
     * Get average mood for a period
     */
    public static function getAverageMood($userId, $startDate, $endDate)
    {
        return self::where('user_id', $userId)
            ->betweenDates($startDate, $endDate)
            ->average('mood_level');
    }

    /**
     * Get mood statistics for a period
     */
    public static function getStatistics($userId, $startDate, $endDate)
    {
        $moods = self::where('user_id', $userId)
            ->betweenDates($startDate, $endDate)
            ->get();

        return [
            'count' => $moods->count(),
            'avg_mood' => round($moods->average('mood_level'), 1),
            'avg_energy' => round($moods->average('energy_level'), 1) ?? null,
            'avg_stress' => round($moods->average('stress_level'), 1) ?? null,
            'avg_focus' => round($moods->average('focus_level'), 1) ?? null,
            'avg_sleep' => round($moods->average('sleep_hours'), 1) ?? null,
            'highest_mood' => $moods->max('mood_level'),
            'lowest_mood' => $moods->min('mood_level'),
        ];
    }

    /**
     * Determine mood category
     */
    public function getMoodCategory()
    {
        if ($this->mood_level >= 8) {
            return 'excellent';
        } elseif ($this->mood_level >= 6) {
            return 'good';
        } elseif ($this->mood_level >= 4) {
            return 'neutral';
        } elseif ($this->mood_level >= 2) {
            return 'poor';
        } else {
            return 'critical';
        }
    }

    /**
     * Get mood trend (improving, stable, declining)
     */
    public static function getTrend($userId, $days = 7)
    {
        $moods = self::where('user_id', $userId)
            ->where('recorded_date', '>=', now()->subDays($days))
            ->orderBy('recorded_date')
            ->pluck('mood_level')
            ->toArray();

        if (count($moods) < 2) {
            return 'insufficient_data';
        }

        $first_half = array_slice($moods, 0, (int)(count($moods) / 2));
        $second_half = array_slice($moods, (int)(count($moods) / 2));

        $avg_first = array_sum($first_half) / count($first_half);
        $avg_second = array_sum($second_half) / count($second_half);

        if ($avg_second > $avg_first + 1) {
            return 'improving';
        } elseif ($avg_second < $avg_first - 1) {
            return 'declining';
        } else {
            return 'stable';
        }
    }

    /**
     * Identify mood correlations (what correlates with mood)
     */
    public static function identifyPatterns($userId)
    {
        $moods = self::where('user_id', $userId)
            ->where('recorded_date', '>=', now()->subDays(90))
            ->get();

        $patterns = [
            'best_days' => [],
            'worst_days' => [],
            'common_activities' => [],
            'weather_correlation' => [],
        ];

        // Find best and worst days
        $bestDays = $moods->sortByDesc('mood_level')->take(5);
        $worstDays = $moods->sortBy('mood_level')->take(5);

        foreach ($bestDays as $mood) {
            $patterns['best_days'][] = $mood->recorded_date->dayName;
        }

        foreach ($worstDays as $mood) {
            $patterns['worst_days'][] = $mood->recorded_date->dayName;
        }

        return $patterns;
    }

    protected function auditSnapshot(): array
    {
        $data = $this->attributesToArray();

        unset($data['updated_at']);

        return $data;
    }
}
