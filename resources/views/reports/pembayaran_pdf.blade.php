<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Pembayaran</title>
    <style>
        @page {
            margin: 100px 25px 25px 25px;
        }

        body {
            font-family: sans-serif;
            font-size: 10px;
            margin: 0;
        }

        .header {
            position: fixed;
            top: -60px;
            left: 0;
            right: 0;
            height: 40px;
            text-align: left;
            border-bottom: 2px solid #333;
            /* Garis pembatas lebih tegas */
            padding-bottom: 5px;
        }

        .header h2 {
            display: none;
            /* Hide h2 in header to move it to body */
        }

        .title-container {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .logo {
            position: absolute;
            right: 0;
            top: 0;
            width: 200px;
            height: auto;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .company-address {
            font-size: 10px;
            color: #555;
            width: 70%;
            /* Batasi lebar agar tidak menabrak logo */
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
        <img src="{{ public_path('images/logomki.png') }}" class="logo" alt="Logo">
        <div class="company-name">PT. Makna Kreatif Indonesia</div>
        <div class="company-address">Jl. Sintraman Jaya I No. 2148, 20 Ilir D II, Kec. Kemuning, Kota Palembang</div>
    </div>

    <div class="title-container">
        <h2>Laporan Data Pembayaran</h2>
    </div>

    @php
        // Kelompokkan data berdasarkan status LUNAS dan BELUM LUNAS
        $groupedData = $dataPembayarans->groupBy(function ($item) {
            $order = $item->order;
            if (!$order) {
                return 'BELUM LUNAS';
            }

            // Hitung total pembayaran untuk order ini
            $totalBayar = $order->dataPembayaran->sum('nominal');
            $grandTotalOrder = $order->grand_total;
            $balanceDue = $grandTotalOrder - $totalBayar;

            return $balanceDue <= 0 ? 'LUNAS' : 'BELUM LUNAS';
        });
    @endphp

    @foreach (['BELUM LUNAS', 'LUNAS'] as $statusGroup)
        @if (isset($groupedData[$statusGroup]) && $groupedData[$statusGroup]->count() > 0)
            <div
                style="margin-top: 20px; margin-bottom: 10px; font-weight: bold; font-size: 12px; border-bottom: 1px solid #000; padding-bottom: 5px;">
                STATUS: {{ $statusGroup }}
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
                        $dataGroup = $groupedData[$statusGroup];
                    @endphp

                    @foreach ($dataGroup as $index => $pembayaran)
                        @php
                            $orderName = $pembayaran->order ? $pembayaran->order->name : 'Tanpa Order';
                        @endphp

                        @if ($currentOrder !== $orderName)
                            {{-- Tampilkan Subtotal untuk grup sebelumnya jika bukan iterasi pertama dalam grup ini --}}
                            @if ($currentOrder !== null)
                                <tr class="subtotal-row">
                                    <td colspan="4" class="text-right">Subtotal {{ $currentOrder }}</td>
                                    <td class="text-right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @php
                                    // Hitung Balance Due untuk grup sebelumnya
                                    // Kita perlu mencari order object yang sesuai dari item sebelumnya yang memiliki orderName yang sama
                                    // Karena $dataGroup adalah collection yang mungkin indexnya tidak urut, kita ambil dari current loop context sebelum direset
                                    // Namun cara paling aman adalah menyimpan referensi order saat loop berjalan
                                    $prevOrder = null;
                                    // Cari item terakhir dari order sebelumnya di dalam grup ini
                                    // Tapi karena kita sudah di blok 'if new order', $subtotal adalah total dari order sebelumnya.
                                    // Kita butuh grand total dari order sebelumnya.
                                    // Karena logic loop ini linear, kita bisa simpan $currentOrderObject sebelum diupdate
                                @endphp
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
                            @php
                                $currentOrder = $orderName;
                                $subtotal = 0; // Reset subtotal untuk order baru
                                $currentOrderObject = $pembayaran->order;
                            @endphp
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

                        {{-- Cek apakah ini item terakhir dalam grup atau item terakhir untuk order ini --}}
                        @php
                            $isLastItem = $loop->last;
                            $nextItem = !$isLastItem ? $dataGroup->values()->get($loop->index + 1) : null;
                            $isLastInOrder =
                                $isLastItem ||
                                ($nextItem &&
                                    ($nextItem->order ? $nextItem->order->name : 'Tanpa Order') !== $currentOrder);
                        @endphp

                        @if ($isLastInOrder)
                            <tr class="subtotal-row">
                                <td colspan="4" class="text-right">Subtotal {{ $currentOrder }}</td>
                                <td class="text-right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @php
                                $prevGrandTotal = $currentOrderObject ? $currentOrderObject->grand_total : 0;
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
                        @endif
                    @endforeach

                    {{-- Total Per Status Group --}}
                    <tr class="total-row">
                        <td colspan="4" class="text-right">TOTAL {{ $statusGroup }}</td>
                        <td class="text-right">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
            <br>
        @endif
    @endforeach
</body>

</html>
