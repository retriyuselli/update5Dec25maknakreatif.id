<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SopRevision extends Model
{
    use HasFactory;

    protected $fillable = [
        'sop_id',
        'title',
        'description',
        'steps',
        'supporting_documents',
        'version',
        'revised_by',
        'revision_notes',
        'revision_date',
    ];

    protected $casts = [
        'steps' => 'array',
        'supporting_documents' => 'array',
        'revision_date' => 'datetime',
    ];

    protected $dates = [
        'revision_date',
    ];

    /**
     * Relationship with SOP
     */
    public function sop(): BelongsTo
    {
        return $this->belongsTo(Sop::class);
    }

    /**
     * Relationship with User who made the revision
     */
    public function revisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revised_by');
    }

    /**
     * Auto-set revision date on creation
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($revision) {
            if (! $revision->revision_date) {
                $revision->revision_date = now();
            }
        });
    }

    /**
     * Get formatted version number
     */
    public function getFormattedVersionAttribute(): string
    {
        return 'v'.$this->version;
    }

    /**
     * Get steps count
     */
    public function getStepsCountAttribute(): int
    {
        return is_array($this->steps) ? count($this->steps) : 0;
    }
}
