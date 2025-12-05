<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SopCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'color',
        'icon',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationship with SOPs
     */
    public function sops(): HasMany
    {
        return $this->hasMany(Sop::class, 'category_id');
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get SOPs count for this category
     */
    public function getSopsCountAttribute(): int
    {
        return $this->sops()->count();
    }

    /**
     * Get active SOPs count for this category
     */
    public function getActiveSopsCountAttribute(): int
    {
        return $this->sops()->active()->count();
    }
}
