<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProspectApp extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'full_name',
        'email',
        'position',
        'phone',
        'company_name',
        'industry_id',
        'name_of_website',
        'service',
        'notes',
        'user_size',
        'harga',
        'bayar',
        'tgl_bayar',
        'reason_for_interest',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'tgl_bayar' => 'date',
        'harga' => 'integer',
        'bayar' => 'integer',
        'status' => 'string',
    ];

    protected $dates = [
        'submitted_at',
        'created_at',
        'updated_at',
        'deleted_at',
        'tgl_bayar',
    ];

    // Relationships
    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Accessors
    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'gray'
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => 'Unknown'
        };
    }
}
