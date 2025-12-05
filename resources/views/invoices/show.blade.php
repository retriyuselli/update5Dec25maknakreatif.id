@extends('layouts.invoice')

@section('title', 'Invoice #' . $order->number)

@section('content')
<div class="container my-5">
    <div class="row no-print mb-4">
        <div class="col-12">
            <a href="{{ route('invoice.download', $order) }}" class="btn btn-primary">
                <i class="fas fa-download"></i> Download PDF
            </a>
            <button class="btn btn-secondary" onclick="window.print()">
                <i class="fas fa-print"></i> Print
            </button>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                Back
            </a>
        </div>
    </div>
    
    <div class="invoice-header row">
        <div class="col-md-6 invoice-company">
            <h2>{{ config('app.name', 'Your Company') }}</h2>
            <h6>{{ config('invoice.address', 'Your Company Address') }}</h6>
            <h6>Phone: {{ config('invoice.phone', '+123456789') }}</h6>
            <h6>Email: {{ config('invoice.email', 'info@yourcompany.com') }}</h6>
        </div>
        <div class="col-md-6 text-md-end">
            <img src="{{ asset(config('invoice.logo', 'images/logo.png')) }}" alt="Company Logo" height="60">
        </div>
    </div>
    
    <div class="invoice-title row">
        <div class="col-12 text-center">
            <h1>INVOICE</h1>
            <h6>#{{ $order->number }}</h6>
        </div>
    </div>
    
    <div class="invoice-details row">
        <div class="col-md-6">
            <h5>Billed To:</h5>
            <address>
                <Strong>Event :</Strong> {{ $order->prospect->name_event }}</><br>
                <strong>Nama : </strong>Nama : CPP_{{ $order->prospect->name_cpp }} & CPW_{{ $order->prospect->name_cpw }}<br>
                <strong>Alamat : </strong>{{ $order->prospect->address }}<br>
                <strong>No. Tlp : </strong>+62{{ $order->prospect->phone }}
            </address>
        </div>
        <div class="col-md-6 text-md-end">
            <h5>Invoice Information:</h5>
            <p>
                <strong>Invoice Date :</strong> {{ now()->format('d F Y') }}<br>
                <strong>Due Date :</strong> {{ $order->due_date ? date('d F Y', strtotime($order->due_date)) : now()->addDays(7)->format('d F Y') }}<br>
                <strong>Status :</strong> 
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
                <table class="table table-bordered table-striped table-items">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" width="4%">No</th>
                            <th width="22%">Product</th>
                            <th width="76%">Vendor</th>
                            {{-- <th class="text-center" width="8%">Stock</th>
                            <th class="text-center" width="8%">Quantity</th>
                            <th class="text-end" width="11%">Unit Price</th>
                            <th class="text-end" width="15%">Subtotal</th>
                            <th class="text-center" width="10%">Status</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold">{{ $item->product->name }}</div>
                                {{-- @if($item->product->description)
                                <div class="text-muted small">{{ Str::limit($item->product->description, 100) }}</div>
                                @endif --}}
                                {{-- @if($item->product->category)
                                <div class="badge bg-secondary mt-1">{{ $item->product->category->name }}</div>
                                @endif --}}
                            </td>
                            <td>
                                @php
                                    $productVendors = $item->product->vendorItems()->with('vendor')->get();
                                @endphp
                                
                                @if($productVendors->count() > 0)
                                    @foreach($productVendors as $productVendor)
                                    <div class="mb-1">
                                        <div class="fw-bold">{{ $productVendor->vendor->name }}</div>
                                        <div class="d-flex justify-content-between small">
                                            <span>{!! ($productVendor->description) !!}</span>
                                            @if(isset($productVendor->quantity) && $productVendor->quantity > 0)
                                            {{-- <span class="badge bg-info">Qty: {{ $productVendor->quantity }}</span> --}}
                                            @endif
                                        </div>
                                        {{-- @if($productVendor->vendor->category)
                                        <div class="badge bg-secondary mt-1">{{ $productVendor->vendor->category->name }}</div>
                                        @endif --}}
                                    </div>
                                    @if(!$loop->last)<hr class="my-1">@endif
                                    @endforeach
                                @else
                                    <span class="text-muted small">No vendor details available</span>
                                @endif
                            </td>
                            {{-- <td class="text-center">{{ $item->stock ?? $item->product->stock }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}</td>
                            <td class="text-center">
                                @if(isset($item->product->is_active) && $item->product->is_active)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td> --}}
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            {{-- <td colspan="4" class="text-end fw-bold">Total Items: {{ $order->items->count() }}</td> --}}
                            <td></td>
                            <td class="text fw-bold">Package Total:</td>
                            <td class="text-end fw-bold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-items">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th width="30%">Product</th>
                            <th class="text-center" width="10%">Stock</th>
                            <th class="text-center" width="10%">Quantity</th>
                            <th class="text-end" width="15%">Unit Price</th>
                            <th class="text-end" width="20%">Subtotal</th>
                            <th class="text-center" width="10%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <div class="fw-bold">{{ $item->product->name }}</div>
                                @if($item->product->description)
                                <div class="text-muted small">{{ Str::limit($item->product->description, 100) }}</div>
                                @endif
                                @if($item->product->category)
                                <div class="badge bg-secondary mt-1">{{ $item->product->category->name }}</div>
                                @endif
                            </td>
                            <td class="text-center">{{ $item->stock ?? $item->product->stock }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}</td>
                            <td class="text-center">
                                @if(isset($item->product->is_active) && $item->product->is_active)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Total Items: {{ $order->items->count() }}</td>
                            <td class="text-end fw-bold">Package Total:</td>
                            <td class="text-end fw-bold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div> --}}
    
    <div class="row">
        <div class="col-md-6">
            @if(!$order->is_paid)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Add Payment</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('invoice.update-payment', $order) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="payment_method_id" class="form-label">Payment Method</label>
                            <select name="payment_method_id" id="payment_method_id" class="form-select" required>
                                <option value="">Select Payment Method</option>
                                @foreach($paymentMethods as $method)
                                <option value="{{ $method->id }}">{{ $method->name }} - {{ $method->no_rekening }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nominal" class="form-label">Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="nominal" name="nominal" required 
                                    min="1" max="{{ $order->sisa }}" value="{{ $order->sisa }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="tgl_bayar" class="form-label">Payment Date</label>
                            <input type="date" class="form-control" id="tgl_bayar" name="tgl_bayar" required 
                                value="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Payment Proof</label>
                            <input type="file" class="form-control" id="image" name="image">
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Notes</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="2"></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Record Payment</button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
            
            <!-- Order Details -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <th>Order Number:</th>
                                <td>{{ $order->number }}</td>
                            </tr>
                            <tr>
                                <th>Contract Number:</th>
                                <td>{{ $order->no_kontrak }}</td>
                            </tr>
                            <tr>
                                <th>Event:</th>
                                <td>{{ $order->prospect->name_event }}</td>
                            </tr>
                            <tr>
                                <th>Capacity:</th>
                                <td>{{ $order->pax }} pax</td>
                            </tr>
                            <tr>
                                <th>Account Manager:</th>
                                <td>{{ $order->user->name ?? 'Not assigned' }}</td>
                            </tr>
                            <tr>
                                <th>Event Manager:</th>
                                <td>{{ $order->employee->name ?? 'Not assigned' }}</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    {{ $order->is_paid ? 'Paid' : 'Unpaid' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Closing Date:</th>
                                <td>{{ $order->closing_date ? date('d F Y', strtotime($order->closing_date)) : '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

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
                            <td>{{ $payment->paymentMethod->name ?? 'N/A' }}</td>
                            <td>{{ $payment->keterangan }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

<!-- Optional: Add a Vendor Summary Section -->
{{-- <div class="row mt-4">
    <div class="col-12">
        <h5>Vendor Summary</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead class="table-secondary">
                    <tr>
                        <th width="5%">#</th>
                        <th width="25%">Vendor</th>
                        <th width="15%">Category</th>
                        <th width="30%">Products Supplied</th>
                        <th width="15%" class="text-end">Published Price</th>
                        <th width="10%" class="text-center">Contract</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $vendors = collect();
                        foreach($order->items as $item) {
                            $productVendors = $item->product->vendorItems()->with(['vendor', 'vendor.category'])->get();
                            foreach($productVendors as $pv) {
                                $vendors->push([
                                    'vendor' => $pv->vendor,
                                    'product' => $item->product->name,
                                    'price' => $pv->harga_publish ?? 0,
                                    'contract' => $pv->kontrak_kerjasama
                                ]);
                            }
                        }
                        $groupedVendors = $vendors->groupBy(function($item) {
                            return $item['vendor']->id;
                        });
                    @endphp
                    
                    @foreach($groupedVendors as $index => $vendorGroup)
                        @php
                            $vendor = $vendorGroup->first()['vendor'];
                            $products = $vendorGroup->pluck('product')->unique()->implode(', ');
                            $totalPrice = $vendorGroup->sum('price');
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="fw-bold">{{ $vendor->name }}</div>
                                @if($vendor->pic_name)
                                <div class="small">PIC: {{ $vendor->pic_name }}</div>
                                @endif
                            </td>
                            <td>{{ $vendor->category->name ?? 'Uncategorized' }}</td>
                            <td>{{ Str::limit($products, 100) }}</td>
                            <td class="text-end">Rp {{ number_format($totalPrice, 0, ',', '.') }}</td>
                            <td class="text-center">
                                @if($vendorGroup->first()['contract'])
                                <span class="badge bg-success">Yes</span>
                                @else
                                <span class="badge bg-warning text-dark">No</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    
                    @if($groupedVendors->count() == 0)
                        <tr>
                            <td colspan="6" class="text-center">No vendor data available</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div> --}}

    <!-- Expenses Section - Add this before footer in show.blade.php if you want to display expenses -->
    {{-- @if($order->expenses->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <h5>Expenses</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">#</th>
                            <th width="20%">Vendor</th>
                            <th width="15%">Date</th>
                            <th width="20%">Description</th>
                            <th width="10%">No. ND</th>
                            <th width="15%" class="text-end">Amount</th>
                            <th width="15%">Payment Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->expenses as $index => $expense)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <span class="fw-bold">{{ $expense->vendor->name ?? 'Unknown Vendor' }}</span>
                                @if(isset($expense->vendor->category))
                                <div class="badge bg-secondary mt-1">{{ $expense->vendor->category->name }}</div>
                                @endif
                            </td>
                            <td>{{ date('d M Y', strtotime($expense->date_expense)) }}</td>
                            <td>{{ $expense->note }}</td>
                            <td>{{ $expense->no_nd }}</td>
                            <td class="text-end">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                            <td>{{ $expense->paymentMethod->name ?? 'Unknown' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="5" class="text-end fw-bold">Total Expenses:</td>
                            <td class="text-end fw-bold">Rp {{ number_format($order->expenses->sum('amount'), 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-end fw-bold">Profit/Loss:</td>
                            <td class="text-end fw-bold {{ $order->laba_kotor >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($order->laba_kotor, 0, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif --}}
    
    <div class="footer row">
        <div class="col-md-8">
            <h5>Terms & Conditions</h5>
            <ul>
                <li>Payment is due within {{ $order->due_date ? now()->diffInDays(strtotime($order->due_date)) : '7' }} days</li>
                <li>Please make payments via bank transfer to the account provided or via the payment methods listed</li>
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
    @media print {
        .no-print {
            display: none;
        }
        
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
</style>
@endsection