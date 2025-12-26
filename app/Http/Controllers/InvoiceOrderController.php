<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentMethod;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\View;

class InvoiceOrderController extends Controller
{
    /**
     * Display the invoice for the given order.
     *
     * @return \Illuminate\View\View
     */
    public function show(Order $order)
    {
        // Get payment methods for the view
        $paymentMethods = PaymentMethod::where('is_cash', false)->get();

        // Get order details with eager loading of all relationships needed for the view
        $order = Order::with([
            'items.product.category',
            'items.product.vendorItems.vendor',
            'prospect',
            'employee',
            'user',
            'dataPembayaran.paymentMethod',
        ])->findOrFail($order->id);

        // Calculate total quantity across all items
        $totalQuantity = $order->items->sum('quantity');

        // Calculate additional order statistics
        $averageUnitPrice = $order->items->count() > 0
            ? $order->items->sum(function ($item) {
                return $item->unit_price;
            }) / $order->items->count()
            : 0;

        // Get order date details
        $eventDate = $order->prospect->date_resepsi;
        $daysUntilEvent = $eventDate ? now()->diffInDays($eventDate, false) : null;

        // Format dates for display
        $formattedEventDate = $eventDate ? date('d F Y', strtotime($eventDate)) : 'Not set';

        return view('invoices.show', compact(
            'order',
            'paymentMethods',
            'totalQuantity',
            'averageUnitPrice',
            'daysUntilEvent',
            'formattedEventDate'
        ));
    }

    /**
     * Generate and download PDF invoice for the given order.
     *
     * @return Response
     */
    public function download(Order $order)
    {
        // Get order details with eager loading for improved performance
        $order = Order::with([
            'items.product.category',
            'items.product.vendorItems.vendor',
            'prospect',
            'employee',
            'user',
            'dataPembayaran.paymentMethod',
        ])->findOrFail($order->id);

        // Configure PDF options to handle page breaks properly
        $pdf = PDF::loadView('invoices.pdf', compact('order'));

        // Set PDF options for better rendering
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'dpi' => 150,
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'isPhpEnabled' => true,
            'isFontSubsettingEnabled' => true,
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
        ]);

        return $pdf->stream("Invoice-{$order->prospect->name_event}.pdf");
    }

    /**
     * Generate PDF for simulation package.
     *
     * @return Response
     */
    public function downloadSimulation(Order $order)
    {
        // Get order details with eager loading
        $order = Order::with([
            'items.product.category',
            'items.product.vendorItems.vendor',
            'prospect',
            'employee',
            'dataPembayaran.paymentMethod',
        ])->findOrFail($order->id);

        // Configure PDF options
        $pdf = PDF::loadView('invoices.simulation-pdf', compact('order'));

        // Set PDF options for better rendering of multi-page documents
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOptions([
            'dpi' => 150,
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'isPhpEnabled' => true,
            'isFontSubsettingEnabled' => true,
        ]);

        return $pdf->download("wedding-package-simulation-{$order->number}.pdf");
    }

    /**
     * Print the invoice for the given order.
     *
     * @return \Illuminate\View\View
     */
    public function print(Order $order)
    {
        $order = Order::with([
            'items.product.vendorItems.vendor',
            'prospect',
            'employee',
            'dataPembayaran.paymentMethod',
        ])->findOrFail($order->id);

        return view('invoices.print', compact('order'));
    }

    /**
     * Update the payment status of the order.
     *
     * @return RedirectResponse
     */
    public function updatePayment(Request $request, Order $order)
    {
        // Validate CSRF token
        if (! $request->hasValidSignature() && ! $request->filled('_token')) {
            return redirect()->route('invoice.show', $order)
                ->with('error', 'Invalid request. Please try again.');
        }

        $validated = $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'nominal' => 'required|numeric|min:1',
            'image' => 'nullable|image|max:2048',
            'tgl_bayar' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ]);

        // Handle file upload if present
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('payment-proofs', 'public');
            $validated['image'] = $path;
        }

        // Create payment record
        $order->dataPembayaran()->create($validated);

        // Check if payment completed
        $totalPaid = $order->dataPembayaran()->sum('nominal');
        if ($totalPaid >= $order->grand_total) {
            $order->update(['is_paid' => true]);
        }

        return redirect()->route('invoice.show', $order)
            ->with('success', 'Payment recorded successfully!');
    }
}
