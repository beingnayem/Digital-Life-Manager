<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'category',
        'color_tag',
        'is_pinned',
        'is_archived',
        'tags',
        'attachments',
        'collaborator_ids',
        'permission_level',
        'word_count',
        'reading_time',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_archived' => 'boolean',
        'tags' => 'array',
        'attachments' => 'array',
        'collaborator_ids' => 'array',
    ];

    /**
     * Get the user that owns the note.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Get only pinned notes
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Scope: Get only archived notes
     */
    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    /**
     * Scope: Get active (not archived) notes
     */
    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }

    /**
     * Scope: Get notes by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: Get shared notes
     */
    public function scopeShared($query)
    {
        return $query->where('permission_level', 'shared');
    }

    /**
     * Scope: Get public notes
     */
    public function scopePublic($query)
    {
        return $query->where('permission_level', 'public');
    }

    /**
     * Scope: Search notes by title or content
     */
    public function scopeSearch($query, $term)
    {
        return $query->whereRaw("MATCH(title, content) AGAINST(? IN BOOLEAN MODE)", [$term]);
    }

    /**
     * Scope: Recently updated notes
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('updated_at', '>=', now()->subDays($days))
                     ->orderBy('updated_at', 'desc');
    }

    /**
     * Calculate word count
     */
    public function calculateWordCount()
    {
        $this->word_count = str_word_count(strip_tags($this->content));
        return $this;
    }

    /**
     * Calculate reading time (average 200 words per minute)
     */
    public function calculateReadingTime()
    {
        $this->reading_time = max(1, ceil($this->word_count / 200));
        return $this;
    }

    /**
     * Pin this note
     */
    public function pin()
    {
        $this->update(['is_pinned' => true]);
    }

    /**
     * Unpin this note
     */
    public function unpin()
    {
        $this->update(['is_pinned' => false]);
    }

    /**
     * Archive this note
     */
    public function archive()
    {
        $this->update(['is_archived' => true]);
    }

    /**
     * Restore from archive
     */
    public function restore()
    {
        $this->update(['is_archived' => false]);
    }

    /**
     * Add collaborator
     */
    public function addCollaborator($userId)
    {
        $collaborators = $this->collaborator_ids ?? [];
        if (!in_array($userId, $collaborators)) {
            $collaborators[] = $userId;
            $this->update(['collaborator_ids' => $collaborators]);
        }
    }

    /**
     * Remove collaborator
     */
    public function removeCollaborator($userId)
    {
        $collaborators = $this->collaborator_ids ?? [];
        $this->update(['collaborator_ids' => array_filter($collaborators, fn($id) => $id !== $userId)]);
    }

    /**
     * Check if user has access
     */
    public function canAccess($userId)
    {
        if ($this->user_id === $userId) {
            return true;
        }

        if ($this->permission_level === 'public') {
            return true;
        }

        if ($this->permission_level === 'shared') {
            return in_array($userId, $this->collaborator_ids ?? []);
        }

        return false;
    }
}
