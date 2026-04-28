<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Get the user that performed the action.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Get logs for a specific action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope: Get logs for a specific entity
     */
    public function scopeByEntity($query, $entityType, $entityId = null)
    {
        $query->where('entity_type', $entityType);

        if ($entityId) {
            $query->where('entity_id', $entityId);
        }

        return $query;
    }

    /**
     * Scope: Get logs in a date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereDate('created_at', '>=', $startDate)
                     ->whereDate('created_at', '<=', $endDate);
    }

    /**
     * Scope: Get recent logs
     */
    public function scopeRecent($query, $minutes = 60)
    {
        return $query->where('created_at', '>=', now()->subMinutes($minutes));
    }

    /**
     * Get human-readable action description
     */
    public function getDescription()
    {
        return sprintf(
            '%s %s the %s (#%s)',
            $this->user->name ?? 'User',
            $this->action,
            $this->entity_type,
            $this->entity_id
        );
    }

    /**
     * Get what changed
     */
    public function getChanges()
    {
        if (!$this->old_values && !$this->new_values) {
            return null;
        }

        $changes = [];

        if ($this->action === 'created') {
            foreach ($this->new_values as $field => $value) {
                $changes[$field] = [
                    'from' => null,
                    'to' => $value,
                ];
            }
        } elseif ($this->action === 'updated') {
            foreach ($this->new_values as $field => $newValue) {
                $oldValue = $this->old_values[$field] ?? null;
                if ($oldValue !== $newValue) {
                    $changes[$field] = [
                        'from' => $oldValue,
                        'to' => $newValue,
                    ];
                }
            }
        } elseif ($this->action === 'deleted') {
            foreach ($this->old_values as $field => $value) {
                $changes[$field] = [
                    'from' => $value,
                    'to' => null,
                ];
            }
        }

        return $changes;
    }
}
