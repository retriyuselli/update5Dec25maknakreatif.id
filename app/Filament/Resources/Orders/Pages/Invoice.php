<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use App\Models\PaymentMethod;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class Invoice extends Page
{
    protected static string $resource = OrderResource::class;

    protected string $view = 'filament.resources.order-resource.pages.invoice';

    protected static ?string $title = 'Detail';

    protected static ?string $slug = 'details';

    public Order $order;

    public function mount(int|string $record): void
    {
        // Eager load semua relasi yang diperlukan untuk mencegah masalah N+1.
        $this->order = Order::with([
            'prospect',
            'user',
            'employee',
            'items.product.vendorItems.vendor',
            'dataPembayaran.paymentMethod',
        ])->findOrFail($record);
    }

    protected function getViewData(): array
    {
        // Menyediakan variabel $paymentMethods ke file view.
        return [
            'paymentMethods' => PaymentMethod::where('is_cash', false)->get(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return 'Invoice '.$this->order->prospect->name_event;
    }
}
