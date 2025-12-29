<?php

namespace App\Http\Controllers;

use App\Models\SimulasiProduk;
use Barryvdh\DomPDF\Facade\Pdf; // Import View
use Illuminate\View\View; // atau use PDF; jika Anda menambahkan alias

class SimulasiDisplayController extends Controller
{
    /**
     * Display the specified simulasi produk.
     */
    public function show(SimulasiProduk $record): View
    {
        $items = collect();
        if ($record->product) {
            // Eager load vendors for items to prevent N+1 queries in the view
            $items = $record->product->items()->with('vendor')->get();
        }

        // The view 'simulasi.show' (likely resources/views/simulasi/invoice.blade.php)
        // expects the SimulasiProduk object as 'simulasi' and items as 'items'.
        // Financial details like subtotal, promo, grand_total can be accessed
        // directly from the 'simulasi' object in the view (e.g., $simulasi->grand_total).
        // The 'SimulasiProduk' model already has accessors for these if needed.

        // Pass 'record' as 'simulasi' to match the view variable name
        return view('simulasi.show', [
            'simulasi' => $record,
            'items' => $items,
            'pengurangans' => $record->pengurangans,
        ]);
    }

    public function downloadPdf(SimulasiProduk $record) // Menggunakan Route Model Binding
    {
        // Ambil item-item dari produk dasar jika ada
        $items = collect();
        if ($record->product) {
            // Asumsi model Product memiliki relasi 'items' ke ProductVendor
            // dan setiap ProductVendor memiliki relasi 'vendor'
            // Eager load vendor untuk menghindari N+1 query di view
            $items = $record->product->items()->with('vendor')->get();
        }

        // Data yang akan dilewatkan ke view
        // Variabel total (subtotal, promo, dll.) sudah ada di $record
        $data = [
            'record' => $record,
            'items' => $items,
            // Anda bisa melewatkan variabel total secara eksplisit jika diperlukan,
            // tapi karena $record sudah memilikinya, ini mungkin tidak perlu.
            // 'subtotal' => $record->total_price,
            // 'promo' => $record->promo,
            // 'penambahan' => $record->penambahan,
            // 'pengurangan' => $record->pengurangan,
            // 'grand_total' => $record->grand_total,
        ];

        // Render view 'simulasi.show' dengan data
        // Pastikan path view sudah benar
        $pdf = Pdf::loadView('pdf.simulasi', $data);

        // Atur ukuran kertas dan orientasi jika perlu (opsional)
        // $pdf->setPaper('a4', 'portrait');

        // Buat nama file PDF yang dinamis
        $fileName = 'simulasi_penawaran_'.$record->slug.'_'.now()->format('Ymd').'.pdf';

        // Download PDF
        return $pdf->download($fileName);

        // Atau jika ingin menampilkan di browser terlebih dahulu (inline)
        // return $pdf->stream($fileName);
    }

    public function draftKontrak(SimulasiProduk $record)
    {
        $items = collect();
        if ($record->product) {
            $items = $record->product->items()->with(['vendor.category'])->get();
        }

        // Calculate Roman Month
        $months = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];
        // Use record's creation date for consistent numbering and date suffix
        $currentMonth = $record->created_at->month;
        $bulanRomawi = $months[$currentMonth];
        $tahun = $record->created_at->year;

        // Calculate Sequence Number based on created_at year
        // This ensures numbering resets every year based on the record's creation time
        $sequence = SimulasiProduk::whereYear('created_at', $record->created_at->year)
            ->where('id', '<=', $record->id)
            ->count();
            
        // Format: nomor surat/MW/KKP/bulan berjalan/tahun berjalan
        $nomorSurat = $sequence . '/MW/KKP/' . $bulanRomawi . '/' . $tahun;

        // Find Finance User
        $financeUser = \App\Models\User::role('Finance')->first();

        $data = [
            'record' => $record,
            'items' => $items,
            'prospect' => $record->prospect,
            'nomorSurat' => $nomorSurat,
            'financeUser' => $financeUser,
        ];

        $pdf = Pdf::loadView('pdf.draft_kontrak', $data);
        $pdf->setPaper('a4', 'portrait');
        
        // Configure DomPDF for better compatibility
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'sans-serif',
        ]);

        $fileName = 'Draft_Kontrak_' . $record->slug . '_' . now()->format('Ymd') . '.pdf';

        return $pdf->stream($fileName);
    }
}
