<?php

namespace App\Filament\Resources\NotaDinas\Pages;

use App\Filament\Resources\NotaDinas\NotaDinasResource;
use App\Models\NotaDinas;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class ViewNd extends Page
{
    protected static string $resource = NotaDinasResource::class;

    protected string $view = 'filament.resources.nota-dinas-resource.pages.view-nd';

    public NotaDinas $notaDinas;

    public $notaDinasDetails;

    public function mount(int|string $record): void
    {
        // Load NotaDinas dengan semua detail-nya dari NotaDinasDetail
        $this->notaDinas = NotaDinas::with([
            'pengirim',
            'penerima',
            'approver',
            'details' => function ($query) {
                $query->select('*'); // Ensure all fields are selected including invoice_file
            },
            'details.vendor',
            'details.order.prospect',
        ])->findOrFail($record);

        // Get all details untuk nota dinas ini dari NotaDinasDetail model
        $this->notaDinasDetails = $this->notaDinas->details;
    }

    protected function getViewData(): array
    {
        // Calculate totals dari NotaDinasDetail
        $totalJumlahTransfer = $this->notaDinasDetails->sum('jumlah_transfer');
        $totalByJenis = $this->notaDinasDetails->groupBy('jenis_pengeluaran')
            ->map(fn ($items) => $items->sum('jumlah_transfer'));

        // Statistik tambahan dari NotaDinasDetail
        $totalInvoices = $this->notaDinasDetails->whereNotNull('invoice_number')->count();
        $paidInvoices = $this->notaDinasDetails->where('status_invoice', 'sudah dibayar')->count();

        return [
            'totalJumlahTransfer' => $totalJumlahTransfer,
            'totalByJenis' => $totalByJenis,
            'details' => $this->notaDinasDetails,
            'totalInvoices' => $totalInvoices,
            'paidInvoices' => $paidInvoices,
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return 'Surat Persetujuan - '.$this->notaDinas->no_nd;
    }

    protected function getActions(): array
    {
        return [
            Action::make('download_pdf')
                ->label('Details')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->url(fn (): string => route('nota-dinas.preview-web', ['notaDinas' => $this->notaDinas->id]))
                ->openUrlInNewTab(),
        ];
    }
}
