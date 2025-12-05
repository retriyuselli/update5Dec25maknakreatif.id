<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotaDinasDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nota_dinas_id',
        'nama_rekening',
        'vendor_id',
        'keperluan',
        'event',
        'jumlah_transfer',
        'invoice_number',
        'invoice_file',
        'bank_name',
        'bank_account',
        'account_holder',
        'status_invoice', // belum dibayar, sudah dibayar, dsb
        'jenis_pengeluaran',
        'payment_stage',
        'order_id',
    ];

    public function notaDinas()
    {
        return $this->belongsTo(NotaDinas::class, 'nota_dinas_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function expense()
    {
        return $this->hasOne(Expense::class, 'nota_dinas_detail_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'nota_dinas_detail_id');
    }

    public function expenseOps()
    {
        return $this->hasMany(ExpenseOps::class, 'nota_dinas_detail_id');
    }

    public function pengeluaranLains()
    {
        return $this->hasMany(PengeluaranLain::class, 'nota_dinas_detail_id');
    }

    public function getFormattedLabelAttribute()
    {
        $vendorName = $this->vendor->name ?? 'N/A';
        $keperluan = $this->keperluan ?? 'N/A';
        $jumlah = number_format($this->jumlah_transfer, 0, ',', '.');
        $paymentStage = $this->payment_stage ? " | {$this->payment_stage}" : '';

        return "{$this->notaDinas->no_nd} | {$vendorName} | {$keperluan}{$paymentStage} | Rp {$jumlah}";
    }
}
