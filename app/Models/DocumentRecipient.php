<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentRecipient extends Model
{
    protected $fillable = [
        'document_id',
        'user_id',
        // 'department_id',
        'read_at',
        'is_cc',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'is_cc' => 'boolean',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
