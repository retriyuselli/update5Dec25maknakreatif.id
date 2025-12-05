<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prospect extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name_event',
        'name_cpp',
        'name_cpw',
        'address',
        'phone',
        'date_lamaran',
        'date_akad',
        'date_resepsi',
        'venue',
        'total_penawaran',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'date_lamaran' => 'date',
        'date_akad' => 'date',
        'date_resepsi' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        // Prevent deletion if prospect has associated orders
        static::deleting(function ($prospect) {
            if ($prospect->orders()->exists()) {
                throw new Exception("Cannot delete prospect '{$prospect->name_event}' because it has associated orders.");
            }
        });
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
