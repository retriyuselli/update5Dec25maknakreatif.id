<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Pembayaran</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .company-address {
            font-size: 10px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .order-group-header {
            background-color: #e6e6e6;
            font-weight: bold;
        }

        .subtotal-row {
            background-color: #f0f0f0;
            font-style: italic;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="company-name">PT. Makna Kreatif Indonesia</div>
        <div class="company-address">Jl. Sintraman Jaya I No. 2148, 20 Ilir D II, Kec. Kemuning, Kota Palembang</div>
        <h2>Laporan Data Pembayaran</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>No. Rekening</th>
                <th class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandTotal = 0;
                $no = 1;
                $currentOrder = null;
                $subtotal = 0;
            @endphp

            @foreach ($dataPembayarans as $index => $pembayaran)
                @php
                    $orderName = $pembayaran->order ? $pembayaran->order->name : 'Tanpa Order';
                @endphp

                @if ($currentOrder !== $orderName)
                    {{-- Tampilkan Subtotal untuk grup sebelumnya jika bukan iterasi pertama --}}
                    @if ($currentOrder !== null)
                        <tr class="subtotal-row">
                            <td colspan="4" class="text-right">Subtotal {{ $currentOrder }}</td>
                            <td class="text-right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @php
                            // Hitung Balance Due untuk grup sebelumnya
                            $prevOrder = $dataPembayarans[$index - 1]->order;
                            $prevGrandTotal = $prevOrder ? $prevOrder->grand_total : 0;
                            $balanceDue = $prevGrandTotal - $subtotal;
                            $statusLunas = $balanceDue <= 0 ? 'LUNAS' : 'BELUM LUNAS';
                            $statusColor = $balanceDue <= 0 ? 'green' : 'red';
                        @endphp
                        <tr class="subtotal-row">
                            <td colspan="4" class="text-right">Grand Total Order</td>
                            <td class="text-right">Rp {{ number_format($prevGrandTotal, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="subtotal-row">
                            <td colspan="4" class="text-right">Sisa Pembayaran (Balance Due)</td>
                            <td class="text-right" style="color: {{ $statusColor }}; font-weight: bold;">
                                Rp {{ number_format($balanceDue, 0, ',', '.') }}
                                <br>
                                <span style="font-size: 8px;">({{ $statusLunas }})</span>
                            </td>
                        </tr>
                        @php $subtotal = 0; @endphp
                    @endif

                    {{-- Header Grup Baru --}}
                    <tr class="order-group-header">
                        <td colspan="5">
                            Project / Order: {{ $orderName }}
                            @if ($pembayaran->order && $pembayaran->order->product)
                                <br>
                                <span style="font-weight: normal; font-size: 9px; color: #555;">
                                    Product: {{ $pembayaran->order->product->name }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @php $currentOrder = $orderName; @endphp
                @endif

                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $pembayaran->tgl_bayar ? \Carbon\Carbon::parse($pembayaran->tgl_bayar)->format('d/m/Y') : '-' }}
                    </td>
                    <td>{{ $pembayaran->keterangan }}</td>
                    <td>{{ $pembayaran->paymentMethod ? $pembayaran->paymentMethod->name : '-' }}</td>
                    <td class="text-right">Rp {{ number_format($pembayaran->nominal, 0, ',', '.') }}</td>
                </tr>
                @php
                    $subtotal += $pembayaran->nominal;
                    $grandTotal += $pembayaran->nominal;
                @endphp

                {{-- Tampilkan Subtotal untuk grup terakhir --}}
                @if ($loop->last)
                    <tr class="subtotal-row">
                        <td colspan="4" class="text-right">Subtotal {{ $currentOrder }}</td>
                        <td class="text-right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @php
                        // Hitung Balance Due untuk grup terakhir
                        $lastOrder = $pembayaran->order;
                        $lastGrandTotal = $lastOrder ? $lastOrder->grand_total : 0;
                        $lastBalanceDue = $lastGrandTotal - $subtotal;
                        $lastStatusLunas = $lastBalanceDue <= 0 ? 'LUNAS' : 'BELUM LUNAS';
                        $lastStatusColor = $lastBalanceDue <= 0 ? 'green' : 'red';
                    @endphp
                    <tr class="subtotal-row">
                        <td colspan="4" class="text-right">Grand Total Order</td>
                        <td class="text-right">Rp {{ number_format($lastGrandTotal, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="subtotal-row">
                        <td colspan="4" class="text-right">Sisa Pembayaran (Balance Due)</td>
                        <td class="text-right" style="color: {{ $lastStatusColor }}; font-weight: bold;">
                            Rp {{ number_format($lastBalanceDue, 0, ',', '.') }}
                            <br>
                            <span style="font-size: 8px;">({{ $lastStatusLunas }})</span>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right">Total Keseluruhan</td>
                <td class="text-right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
