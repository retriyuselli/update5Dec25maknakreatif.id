<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class InternalMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subject',
        'message',
        'type',
        'priority',
        'status',
        'sender_id',
        'recipient_ids',
        'cc_ids',
        'bcc_ids',
        'attachments',
        'requires_response',
        'due_date',
        'read_at',
        'replied_at',
        'parent_id',
        'thread_count',
        'tags',
        'department',
        'is_public',
        'is_pinned',
        'read_by',
        'deleted_by',
        'expires_at',
    ];

    protected $casts = [
        'recipient_ids' => 'array',
        'cc_ids' => 'array',
        'bcc_ids' => 'array',
        'attachments' => 'array',
        'tags' => 'array',
        'read_by' => 'array',
        'deleted_by' => 'array',
        'requires_response' => 'boolean',
        'is_public' => 'boolean',
        'is_pinned' => 'boolean',
        'due_date' => 'datetime',
        'read_at' => 'datetime',
        'replied_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Set default values for required fields if not provided
            if (empty($model->recipient_ids)) {
                $model->recipient_ids = [];
            }
            if (empty($model->read_by)) {
                $model->read_by = [];
            }
            if (empty($model->deleted_by)) {
                $model->deleted_by = [];
            }
            if (is_null($model->thread_count)) {
                $model->thread_count = 0;
            }
            if (empty($model->sender_id) && Auth::check()) {
                $model->sender_id = Auth::id();
            }
        });
    }

    // Relationships
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function allReplies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->with('allReplies');
    }

    // Helper methods
    public function getRecipients()
    {
        return User::whereIn('id', $this->recipient_ids ?? [])->get();
    }

    public function getCcRecipients()
    {
        return User::whereIn('id', $this->cc_ids ?? [])->get();
    }

    public function getBccRecipients()
    {
        return User::whereIn('id', $this->bcc_ids ?? [])->get();
    }

    public function getAllRecipients()
    {
        $allIds = array_merge(
            $this->recipient_ids ?? [],
            $this->cc_ids ?? [],
            $this->bcc_ids ?? []
        );

        return User::whereIn('id', array_unique($allIds))->get();
    }

    public function markAsRead($userId = null)
    {
        $userId = $userId ?? Auth::id();
        $userIdInt = (int) $userId;
        $readBy = $this->read_by ?? [];

        // Check if user is already in read_by (handle both int and string formats)
        $isAlreadyRead = in_array($userIdInt, $readBy) || in_array((string) $userIdInt, $readBy);

        if (! $isAlreadyRead) {
            $readBy[] = $userIdInt; // Always store as integer for consistency
            $this->update([
                'read_by' => $readBy,
                'read_at' => now(),
            ]);
        }
    }

    public function isReadBy($userId = null): bool
    {
        $userId = $userId ?? Auth::id();
        $userIdInt = (int) $userId;
        $readBy = $this->read_by ?? [];

        // Check both integer and string formats
        return in_array($userIdInt, $readBy) || in_array((string) $userIdInt, $readBy);
    }

    public function isRecipient($userId = null): bool
    {
        $userId = $userId ?? Auth::id();
        $userIdInt = (int) $userId;
        $userIdString = (string) $userId;

        $allRecipients = array_merge(
            $this->recipient_ids ?? [],
            $this->cc_ids ?? [],
            $this->bcc_ids ?? []
        );

        // Check both integer and string formats
        return in_array($userIdInt, $allRecipients) || in_array($userIdString, $allRecipients);
    }

    public function getOriginalThreadSubject(): string
    {
        // If this is already the root message
        if (! $this->parent_id) {
            return $this->subject;
        }

        // Find the root message by traversing up the parent chain
        $current = $this;
        $maxDepth = 10; // Prevent infinite loops
        $depth = 0;

        while ($current->parent_id && $depth < $maxDepth) {
            $parent = self::find($current->parent_id);
            if (! $parent) {
                break;
            }
            $current = $parent;
            $depth++;
        }

        return $current->subject;
    }

    public function getCleanSubject(): string
    {
        $subject = $this->getOriginalThreadSubject();

        // Remove "Re:" prefixes to get the clean original subject
        $subject = preg_replace('/^(Re:\s*)+/i', '', $subject);

        return trim($subject);
    }

    public function canReply($userId = null): bool
    {
        $userId = $userId ?? Auth::id();

        // User must be recipient or sender to reply
        $isAuthorized = $this->isRecipient($userId) || $this->sender_id == $userId;

        // Reply button only shows if requires_response is true
        return $isAuthorized && $this->requires_response;
    }

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'instruction' => 'Instruksi',
            'communication' => 'Komunikasi',
            'announcement' => 'Pengumuman',
            'task' => 'Tugas',
            'reminder' => 'Pengingat',
            'feedback' => 'Feedback',
            'urgent' => 'Mendesak',
            default => 'Komunikasi'
        };
    }

    public function getPriorityLabel(): string
    {
        return match ($this->priority) {
            'low' => 'Rendah',
            'normal' => 'Normal',
            'high' => 'Tinggi',
            'urgent' => 'Mendesak',
            default => 'Normal'
        };
    }

    public function getPriorityColor(): string
    {
        return match ($this->priority) {
            'low' => 'success',
            'normal' => 'primary',
            'high' => 'warning',
            'urgent' => 'danger',
            default => 'primary'
        };
    }

    // Per-user deletion methods
    public function deleteForUser($userId = null)
    {
        $userId = $userId ?? Auth::id();
        $userIdInt = (int) $userId;
        $deletedBy = $this->deleted_by ?? [];

        if (! in_array($userIdInt, $deletedBy)) {
            $deletedBy[] = $userIdInt;
            $this->update(['deleted_by' => $deletedBy]);
        }
    }

    public function restoreForUser($userId = null)
    {
        $userId = $userId ?? Auth::id();
        $userIdInt = (int) $userId;
        $deletedBy = $this->deleted_by ?? [];

        $deletedBy = array_filter($deletedBy, function ($id) use ($userIdInt) {
            return (int) $id !== $userIdInt;
        });

        $this->update(['deleted_by' => array_values($deletedBy)]);
    }

    public function isDeletedByUser($userId = null): bool
    {
        $userId = $userId ?? Auth::id();
        $userIdInt = (int) $userId;
        $deletedBy = $this->deleted_by ?? [];

        return in_array($userIdInt, $deletedBy) || in_array((string) $userIdInt, $deletedBy);
    }

    // Scopes
    public function scopeForUser($query, $userId = null)
    {
        $userId = $userId ?? Auth::id();
        $userIdString = (string) $userId;

        return $query->where(function ($q) use ($userId, $userIdString) {
            $q->where('sender_id', $userId)
                ->orWhereJsonContains('recipient_ids', $userId)
                ->orWhereJsonContains('recipient_ids', $userIdString)
                ->orWhereJsonContains('cc_ids', $userId)
                ->orWhereJsonContains('cc_ids', $userIdString)
                ->orWhereJsonContains('bcc_ids', $userId)
                ->orWhereJsonContains('bcc_ids', $userIdString)
                ->orWhere('is_public', true);
        })->whereJsonDoesntContain('deleted_by', $userId)
            ->whereJsonDoesntContain('deleted_by', $userIdString);
    }

    public function scopeDeletedByUser($query, $userId = null)
    {
        $userId = $userId ?? Auth::id();
        $userIdString = (string) $userId;

        return $query->where(function ($q) use ($userId, $userIdString) {
            $q->whereJsonContains('deleted_by', $userId)
                ->orWhereJsonContains('deleted_by', $userIdString);
        });
    }

    public function scopeUnread($query, $userId = null)
    {
        $userId = $userId ?? Auth::id();
        $userIdString = (string) $userId;

        return $query->where(function ($q) use ($userId, $userIdString) {
            $q->whereNull('read_by')
                ->orWhere(function ($subQ) use ($userId, $userIdString) {
                    $subQ->whereJsonDoesntContain('read_by', $userId)
                        ->whereJsonDoesntContain('read_by', $userIdString);
                });
        });
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeRequiringResponse($query)
    {
        return $query->where('requires_response', true);
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }
}
