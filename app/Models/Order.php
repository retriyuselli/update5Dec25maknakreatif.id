<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'prospect_id',
        'slug',
        'name',
        'number',
        'user_id',
        'employee_id',
        'last_edited_by',
        'no_kontrak',
        'doc_kontrak',
        'pax',
        'note',
        'total_price',
        'paid_amount',
        'promo',
        'penambahan',
        'pengurangan',
        'grand_total',
        'change_amount',
        'is_paid',
        'closing_date',
        'status',
        'kategori_transaksi',
    ];

    protected $casts = [
        'bukti' => 'array',
        'status' => OrderStatus::class,
        'is_paid' => 'boolean',
        'total_price' => 'decimal:2',
        'promo' => 'decimal:2',
        'penambahan' => 'decimal:2',
        'pengurangan' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'bayar' => 'decimal:2',
        'closing_date' => 'date',
        'kategori_transaksi' => 'string',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Order $order) {
            // Saat sebuah Order dihapus, hapus juga semua relasi terkait.
            // Ini memastikan tidak ada data 'yatim' (orphaned records) di database.
            $order->expenses()->each(fn ($expense) => $expense->delete());
            $order->dataPembayaran()->each(fn ($pembayaran) => $pembayaran->delete());
            // Anda juga bisa menambahkan relasi lain di sini jika perlu, contoh:
            // $order->items()->each(fn ($item) => $item->delete());
        });
    }

    public function getPendapatanDpAttribute()
    {
        return $this->getBayarAttribute();
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Super admin and finance can access all orders
        if (Auth::check()) {
            $user = Auth::user();
            if ($user && ($user->hasRole('super_admin') || $user->hasRole('Finance'))) {
                return $query;
            }
        }

        // Other users can only access their own orders (as Account Manager)
        return $query->where('user_id', Auth::user()->id);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function prospect()
    {
        return $this->belongsTo(Prospect::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function lastEditedBy()
    {
        return $this->belongsTo(User::class, 'last_edited_by');
    }

    public function orderPenambahans()
    {
        return $this->hasMany(OrderPenambahan::class);
    }

    public function orderPengurangans()
    {
        return $this->hasMany(OrderPengurangan::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderProduct::class, 'order_id');
    }

    public function itemsProspect(): HasMany
    {
        return $this->hasMany(Prospect::class, 'prospect_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productName()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderProduct(): BelongsTo
    {
        return $this->belongsTo(OrderProduct::class);
    }

    public function calculateTotalPrice(): float
    {
        $totalPrice = 0;
        foreach ($this->items as $item) {
            $totalPrice += $item->quantity * $item->unit_price;
        }

        return $totalPrice;
    }

    public function dataPembayaran(): HasMany
    {
        return $this->hasMany(DataPembayaran::class);
    }

    // Uang Dibayar
    public function getBayarAttribute()
    {
        $totalPayment = $this->dataPembayaran->sum('nominal');

        return $totalPayment;
    }

    // Sisa Pembayaran
    public function getSisaAttribute()
    {
        $totalPayment = $this->dataPembayaran->sum('nominal');

        return ($this->calculateTotalPrice() + $this->penambahan - $this->promo - $this->pengurangan) - $totalPayment;
    }

    public function dataPengeluaran(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    // Total Pengeluaran
    public function getTotPengeluaranAttribute()
    {
        return $this->dataPengeluaran->sum('amount');
    }

    // Total Paket Akhir
    public function getGrandTotalAttribute()
    {
        return $this->total_price + $this->penambahan - $this->promo - $this->pengurangan;
    }

    // Laba Rugi
    public function getTotSisaAttribute()
    {
        return $this->getBayarAttribute() - $this->getTotPengeluaranAttribute();
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getTotPriceAttribute()
    {
        $totalPriceClosing = $this->sum('total_price');

        return $totalPriceClosing;
    }

    public function getClosing()
    {
        return $this->orders->totPrice;
    }

    public function setProspectAttribute($value)
    {
        $prospect = Prospect::find($value);
        $slug = $this->generateUniqueSlug($prospect->name_event);
        $this->attributes['prospect_id'] = $value;
        $this->attributes['slug'] = $slug;
    }

    public function prospects()
    {
        return $this->hasMany(Prospect::class);
    }

    public function getPendapatanAttribute()
    {
        return $this->getBayarAttribute() + $this->penambahan;
    }

    public function getPengeluaranAttribute()
    {
        return $this->pengurangan + $this->promo + $this->getTotPengeluaranAttribute();
    }

    // Laba Kotor
    public function getLabaKotorAttribute()
    {
        return $this->getGrandTotalAttribute() - $this->getTotPengeluaranAttribute();
    }

    public function getLabaBersihAttribute()
    {
        return $this->getPendapatanDpAttribute() - $this->getTotPengeluaranAttribute();
    }

    public function calculateProfit()
    {
        return $this->total_price
            - $this->promo
            - $this->pengurangan
            + $this->penambahan;
    }

    public function getUangDiterimaAttribute()
    {
        return $this->getBayarAttribute() - $this->getTotPengeluaranAttribute();
    }

    /**
     * Calculate and set grand_total before saving
     */
    public function calculateAndSetGrandTotal()
    {
        $this->grand_total = $this->total_price + $this->penambahan - $this->promo - $this->pengurangan;
    }

    /**
     * Boot method untuk auto-calculate grand_total
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($order) {
            // Auto calculate grand_total before saving
            $order->calculateAndSetGrandTotal();
        });
    }
}
