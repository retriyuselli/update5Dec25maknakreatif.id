<?php

namespace App\Http\Controllers;

use App\Models\NotaDinas;
use Barryvdh\DomPDF\Facade\Pdf;

class NotaDinasPdfController extends Controller
{
    public function downloadPdf(NotaDinas $notaDinas)
    {
        // Load NotaDinas dengan semua relasi yang diperlukan
        $notaDinas->load([
            'pengirim',
            'penerima',
            'approver',
            'details.vendor',
            'details.order.prospect',
        ]);

        // Get details dan perhitungan
        $details = $notaDinas->details;
        $totalJumlahTransfer = $details->sum('jumlah_transfer');
        $totalByJenis = $details->groupBy('jenis_pengeluaran')
            ->map(fn ($items) => $items->sum('jumlah_transfer'));

        // Statistik tambahan
        $totalInvoices = $details->whereNotNull('invoice_number')->count();
        $paidInvoices = $details->where('status_invoice', 'sudah dibayar')->count();

        // Data untuk PDF
        $data = [
            'notaDinas' => $notaDinas,
            'details' => $details,
            'totalJumlahTransfer' => $totalJumlahTransfer,
            'totalByJenis' => $totalByJenis,
            'totalInvoices' => $totalInvoices,
            'paidInvoices' => $paidInvoices,
        ];

        // Generate PDF
        $pdf = Pdf::loadView('pdf.nota-dinas-approval', $data);
        $pdf->setPaper('A4', 'portrait');

        // Download dengan nama file yang sesuai
        $filename = 'approval-'.$notaDinas->no_nd.'-'.now()->format('Y-m-d').'.pdf';

        return $pdf->download($filename);
    }

    public function previewPdf(NotaDinas $notaDinas)
    {
        // Load NotaDinas dengan semua relasi yang diperlukan
        $notaDinas->load([
            'pengirim',
            'penerima',
            'approver',
            'details.vendor',
            'details.order.prospect',
        ]);

        // Get details dan perhitungan
        $details = $notaDinas->details;
        $totalJumlahTransfer = $details->sum('jumlah_transfer');
        $totalByJenis = $details->groupBy('jenis_pengeluaran')
            ->map(fn ($items) => $items->sum('jumlah_transfer'));

        // Statistik tambahan
        $totalInvoices = $details->whereNotNull('invoice_number')->count();
        $paidInvoices = $details->where('status_invoice', 'sudah dibayar')->count();

        // Data untuk PDF
        $data = [
            'notaDinas' => $notaDinas,
            'details' => $details,
            'totalJumlahTransfer' => $totalJumlahTransfer,
            'totalByJenis' => $totalByJenis,
            'totalInvoices' => $totalInvoices,
            'paidInvoices' => $paidInvoices,
        ];

        // Stream PDF untuk preview
        $pdf = Pdf::loadView('pdf.nota-dinas-approval', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('preview-'.$notaDinas->no_nd.'.pdf');
    }

    public function previewWeb(NotaDinas $notaDinas)
    {
        // Load NotaDinas dengan semua relasi yang diperlukan
        $notaDinas->load([
            'pengirim',
            'penerima',
            'approver',
            'details.vendor',
            'details.order.prospect',
        ]);

        // Get details dan perhitungan
        $details = $notaDinas->details;
        $totalJumlahTransfer = $details->sum('jumlah_transfer');
        $totalByJenis = $details->groupBy('jenis_pengeluaran')
            ->map(fn ($items) => $items->sum('jumlah_transfer'));

        // Statistik tambahan
        $totalInvoices = $details->whereNotNull('invoice_number')->count();
        $paidInvoices = $details->where('status_invoice', 'sudah dibayar')->count();

        // Data untuk web preview
        $data = [
            'notaDinas' => $notaDinas,
            'details' => $details,
            'totalJumlahTransfer' => $totalJumlahTransfer,
            'totalByJenis' => $totalByJenis,
            'totalInvoices' => $totalInvoices,
            'paidInvoices' => $paidInvoices,
        ];

        // Return web view yang sama dengan PDF template tapi dengan styling web
        return view('pdf.nota-dinas-preview', $data);
    }
}
