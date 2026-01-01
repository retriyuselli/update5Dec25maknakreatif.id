<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Document extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'document_number',
        // 'recipient_id', // Removed in favor of relation
        'title',
        'summary',
        'content',
        'metadata',
        'date_effective',
        'date_expired',
        'status',
        'confidentiality',
        'created_by',
        'use_digital_signature',
        'show_confidentiality_warning',
    ];

    protected $casts = [
        'metadata' => 'array',
        'date_effective' => 'date',
        'date_expired' => 'date',
        'use_digital_signature' => 'boolean',
        'show_confidentiality_warning' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class);
    }

    // public function recipient(): BelongsTo
    // {
    //     return $this->belongsTo(User::class, 'recipient_id');
    // }

    public function recipientsList(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'document_recipients', 'document_id', 'user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(DocumentApproval::class)->orderBy('step_order');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(DocumentRecipient::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(DocumentAttachment::class);
    }
}
