<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Sop extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'category_id',
        'steps',
        'supporting_documents',
        'version',
        'is_active',
        'effective_date',
        'review_date',
        'created_by',
        'updated_by',
        'keywords',
    ];

    protected $casts = [
        'steps' => 'array',
        'supporting_documents' => 'array',
        'effective_date' => 'date',
        'review_date' => 'date',
        'is_active' => 'boolean',
    ];

    protected $dates = [
        'effective_date',
        'review_date',
        'deleted_at',
    ];

    /**
     * Relationship with SOP Category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(SopCategory::class);
    }

    /**
     * Relationship with User who created the SOP
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship with User who last updated the SOP
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Relationship with SOP revisions/versions
     */
    public function revisions(): HasMany
    {
        return $this->hasMany(SopRevision::class);
    }

    /**
     * Scope for active SOPs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for searching SOPs
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('keywords', 'like', "%{$search}%");
        });
    }

    /**
     * Scope for filtering by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
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

    /**
     * Check if SOP needs review
     */
    public function needsReview(): bool
    {
        return $this->review_date && $this->review_date->isPast();
    }

    /**
     * Get next version number
     */
    public function getNextVersion(): string
    {
        $currentVersion = (float) $this->version;

        return number_format($currentVersion + 0.1, 1);
    }

    /**
     * Create a new revision when updating
     */
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($sop) {
            // Create revision before updating
            SopRevision::create([
                'sop_id' => $sop->id,
                'title' => $sop->getOriginal('title'),
                'description' => $sop->getOriginal('description'),
                'steps' => $sop->getOriginal('steps'),
                'supporting_documents' => $sop->getOriginal('supporting_documents'),
                'version' => $sop->getOriginal('version'),
                'revised_by' => Auth::id() ?? 1,
                'revision_notes' => 'Automatic revision before update',
            ]);
        });
    }
}
