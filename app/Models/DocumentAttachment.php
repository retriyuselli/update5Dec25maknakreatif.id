<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentAttachment extends Model
{
    protected $fillable = [
        'document_id',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
