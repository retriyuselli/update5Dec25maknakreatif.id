<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Company extends Model
{
    protected $fillable = [
        'company_name',
        'business_license',
        'owner_name',
        'email',
        'phone',
        'address',
        'city',
        'province',
        'postal_code',
        'website',
        'description',
        'logo_url',
        'favicon_url',
        'image_login',
        'established_year',
        'employee_count',
        'legal_entity_type',
        'deed_of_establishment',
        'deed_date',
        'notary_name',
        'notary_license_number',
        'nib_number',
        'nib_issued_date',
        'nib_valid_until',
        'npwp_number',
        'npwp_issued_date',
        'tax_office',
        'legal_documents',
        'legal_document_status',
        'payment_method_id',
    ];

    protected $casts = [
        'established_year' => 'integer',
        'employee_count' => 'integer',
        'deed_date' => 'date',
        'nib_issued_date' => 'date',
        'nib_valid_until' => 'date',
        'npwp_issued_date' => 'date',
        'legal_documents' => 'array',
    ];

    public function getFaviconPathAttribute(): string
    {
        if ($this->favicon_url && Storage::disk('public')->exists($this->favicon_url)) {
            return Storage::disk('public')->path($this->favicon_url);
        }

        return public_path('images/favicon_makna.png');
    }

    public function getFaviconPublicUrlAttribute(): string
    {
        if ($this->favicon_url && Storage::disk('public')->exists($this->favicon_url)) {
            return Storage::disk('public')->url($this->favicon_url);
        }

        return asset('images/favicon_makna.png');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
