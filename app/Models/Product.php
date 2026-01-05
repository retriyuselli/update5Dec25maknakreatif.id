<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'stock',
        'product_price', // Penjumlahan dari repeater harga publish dari vendor
        'price',
        'is_active',
        'pax',
        'description',
        'image',
        'is_approved',
        'pengurangan',
        'penambahan',
        'penambahan_publish',
        'penambahan_vendor',
        'last_edited_by_id',
        'parent_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_approved' => 'boolean',
        'product_price' => 'integer',
        'pengurangan' => 'integer',
        'penambahan' => 'integer',
        'penambahan_publish' => 'integer',
        'penambahan_vendor' => 'integer',
        'price' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (Auth::check()) {
                $model->last_edited_by_id = Auth::id();
            }
        });
    }

    public function pengurangans()
    {
        return $this->hasMany(ProductPengurangan::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductVendor::class);
    }

    public function itemsPengurangan(): HasMany
    {
        return $this->hasMany(ProductPengurangan::class);
    }

    public function penambahanHarga(): HasMany
    {
        return $this->hasMany(ProductPenambahan::class);
    }

    public function vendorItems(): HasMany
    {
        return $this->hasMany(ProductVendor::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function lastEditedBy()
    {
        return $this->belongsTo(User::class, 'last_edited_by_id');
    }

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function parent()
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Product::class, 'parent_id');
    }

    public function orders()
    {
        return $this->belongsToMany(
            Order::class,
            'order_products', // Nama tabel pivot
            'product_id',     // Foreign key untuk Product di tabel pivot
            'order_id'        // Foreign key untuk Order di tabel pivot
        );
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public static function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (self::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function orderItems()
    {
        // Pastikan namespace App\Models\OrderItem sudah benar
        return $this->hasMany(OrderProduct::class);
    }

    public function getVendorTotalAttribute()
    {
        return (int) $this->items()->sum('total_price');
    }
}
