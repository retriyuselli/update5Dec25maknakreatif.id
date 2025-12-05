@extends('layouts.invoice')

@section('title', 'Print Invoice #' . $order->number)

@section('content')
<div class="container my-5">
    <div class="invoice-header row">
        <div class="col-md-6 invoice-company">
            <h2>{{ config('app.name', 'Your Company') }}</h2>
            <p>{{ config('invoice.address', 'Your Company Address') }}</p>
            <p>Phone: {{ config('invoice.phone', '+123456789') }}</p>
            <p>Email: {{ config('invoice.email', 'info@yourcompany.com') }}</p>
        </div>
        <div class="col-md-6 text-md-end">
            <img src="{{ asset(config('invoice.logo', 'images/logo.png')) }}" alt="Company Logo" height="80">
        </div>
    </div>
    
    <div class="invoice-title row">
        <div class="col-12 text-center">
            <h1>INVOICE PRINT</h1>
            <h4>#{{ $order->number }}</h4>
        </div>
    </div>
    
    <div class="invoice-details row">
        <div class="col-md-6">
            <h5>Billed To:</h5>
            <address>
                <strong>{{ $order->prospect->name_event }}</strong><br>
                {{ $order->prospect->name_cpp }} & {{ $order->prospect->name_cpw }}<br>
                {{ $order->prospect->address }}<br>
                {{ $order->prospect->phone }}
            </address>
        </div>
        <div class="col-md-6 text-md-end">
            <h5>Invoice Information:</h5>
            <p>
                <strong>Invoice Date:</strong> {{ now()->format('d F Y') }}<br>
                <strong>Due Date:</strong> {{ $order->due_date ? date('d F Y', strtotime($order->due_date)) : now()->addDays(7)->format('d F Y') }}<br>
                <strong>Status:</strong> 
                @if($order->is_paid)
                    <span class="badge bg-success">Paid</span>
                @else
                    <span class="badge bg-warning text-dark">Pending</span>
                @endif
            </p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-items">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Item</th>
                            <th class="text-end">Quantity</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->product->name }}</td>
                            <td class="text-end">{{ $item->quantity }}</td>
                            <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6"></div>
        <div class="col-md-6">
            <table class="table table-totals">
                <tr>
                    <td>Subtotal</td>
                    <td class="text-end">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                </tr>
                @if($order->penambahan > 0)
                <tr>
                    <td>Additional Fee</td>
                    <td class="text-end">Rp {{ number_format($order->penambahan, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($order->promo > 0)
                <tr>
                    <td>Discount</td>
                    <td class="text-end">Rp {{ number_format($order->promo, 0, ',', '.') }}</td>
                </tr>
                @endif
                @if($order->pengurangan > 0)
                <tr>
                    <td>Reduction</td>
                    <td class="text-end">Rp {{ number_format($order->pengurangan, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr>
                    <th>Total</th>
                    <th class="text-end">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</th>
                </tr>
                <tr>
                    <td>Paid Amount</td>
                    <td class="text-end">Rp {{ number_format($order->bayar, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Balance Due</th>
                    <th class="text-end">Rp {{ number_format($order->sisa, 0, ',', '.') }}</th>
                </tr>
            </table>
        </div>
    </div>
    
    <!-- Payment History -->
    @if(count($order->dataPembayaran) > 0)
    <div class="row mt-4">
        <div class="col-12">
            <h5>Payment History</h5>
            <div class="table-responsive">
                <table class="table table-payments">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->dataPembayaran as $payment)
                        <tr>
                            <td>{{ date('d F Y', strtotime($payment->tgl_bayar)) }}</td>
                            <td>Rp {{ number_format($payment->nominal, 0, ',', '.') }}</td>
                            <td>{{ $payment->paymentMethod->name }}</td>
                            <td>{{ $payment->keterangan }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
    
    <div class="footer row">
        <div class="col-md-8">
            <h5>Terms & Conditions</h5>
            <ul>
                <li>Payment is due within {{ $order->due_date ? now()->diffInDays(strtotime($order->due_date)) : '7' }} days</li>
                <li>Please make payments via bank transfer to the account provided</li>
                <li>For questions, contact our customer service</li>
            </ul>
        </div>
        <div class="col-md-4 text-md-end">
            <p class="mb-5">Thank you for your business!</p>
            <div class="mt-4">
                <div>Approved by:</div>
                <div class="mt-5">________________</div>
                <div>{{ $order->employee->name ?? 'Manager' }}</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    body {
        padding: 0;
        margin: 0;
    }
    
    .container {
        width: 100%;
        max-width: 100%;
        padding: 0;
        margin: 0;
    }
    
    .invoice-header {
        padding: 20px 0;
        border-bottom: 1px solid #ddd;
    }
    
    .invoice-company {
        margin-bottom: 20px;
    }
    
    .invoice-title {
        margin: 30px 0;
    }
    
    .invoice-details {
        margin-bottom: 20px;
    }
    
    .table-items th {
        background-color: #f8f9fa;
    }
    
    .table-totals {
        margin-top: 30px;
    }
    
    .table-payments {
        margin-top: 30px;
    }
    
    .footer {
        margin-top: 50px;
        border-top: 1px solid #ddd;
        padding-top: 20px;
    }

    @media print {
        body {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .badge-success {
            background-color: #28a745 !important;
            color: white !important;
        }
        
        .badge-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    // Auto-print when page loads
    window.onload = function() {
        window.print();
    }
</script>
@endsection