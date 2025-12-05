<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'email',
        'instagram',
        'kontrak',
        'phone',
        'address',
        'position',
        'salary',
        'date_of_birth',
        'date_of_join',
        'date_of_out',
        'no_rek',
        'user_id',
        'bank_name',
        'photo',
        'note',
    ];

    public function prospects(): HasMany
    {
        return $this->hasMany(Prospect::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dataPribadi(): HasOne
    {
        return $this->hasOne(DataPribadi::class, 'email', 'email');
    }

    public function getEmCountAttribute()
    {
        $totEM = Order::where('employee_id', $this->id)->count();

        return $totEM;
    }

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'date_of_join' => 'date',
            'date_of_out' => 'date',
            'salary' => 'decimal:2', // Sesuaikan dengan tipe data di database Anda
        ];
    }
}
