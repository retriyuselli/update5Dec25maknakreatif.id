<?php

namespace App\Filament\Pages;

use App\Enums\OrderStatus;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;

class NetCashFlowReport extends Page
{
    use HasPageShield;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.pages.net-cash-flow-report';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?string $title = 'Laporan Arus Kas Bersih (Net Cash Flow)';

    public $orders;

    #[Url]
    public $status = 'processing';
    public $totalPaymentsAll;
    public $totalExpensesAll;
    public $totalNetCashFlowAll;
    public $pageTitle;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        // Default to processing if invalid status provided
        $statusEnum = OrderStatus::tryFrom($this->status) ?? OrderStatus::Processing;
        
        $this->orders = Order::where('status', $statusEnum)
            ->with(['dataPembayaran', 'expenses', 'prospect', 'user', 'employee', 'items.product.parent'])
            ->get()
            ->map(function ($order) {
                $totalPayments = $order->dataPembayaran->sum('nominal');
                $totalExpenses = $order->expenses->sum('amount');
                $netCashFlow = $totalPayments - $totalExpenses;
                
                $order->total_payments_received = $totalPayments;
                $order->total_expenses_incurred = $totalExpenses;
                $order->net_cash_flow = $netCashFlow;
                
                return $order;
            })
            ->sortByDesc('net_cash_flow');

        $this->totalPaymentsAll = $this->orders->sum('total_payments_received');
        $this->totalExpensesAll = $this->orders->sum('total_expenses_incurred');
        $this->totalNetCashFlowAll = $this->orders->sum('net_cash_flow');

        $this->pageTitle = 'Laporan Arus Kas Bersih (Net Cash Flow) - Order Status: ' . Str::ucfirst($this->status);
    }

    public function downloadPdf()
    {
        $this->loadData(); // Ensure data is loaded

        $pdf = Pdf::loadView('reports.net-cash-flow-pdf', [
            'orders' => $this->orders,
            'status' => $this->status,
            'totalPaymentsAll' => $this->totalPaymentsAll,
            'totalExpensesAll' => $this->totalExpensesAll,
            'totalNetCashFlowAll' => $this->totalNetCashFlowAll,
            'pageTitle' => $this->pageTitle,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'Laporan_Net_Cash_Flow_' . now()->format('Y-m-d_H-i') . '.pdf');
    }
}
