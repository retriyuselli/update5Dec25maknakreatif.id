<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Laba Rugi</title>
    <style>
        .container {
            width: 100%;
            /* Full width for PDF */
            margin: 20px auto;
            /* Adjust margin as needed */
            padding: 0 20px;
            /* Padding for content */
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            line-height: 1;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 5px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #e9e9e9;
            font-weight: 700;
            /* Menggunakan berat bold dari Noto Sans */
        }

        .total-row td {
            font-weight: bold;
            background-color: #f5f5f5;
        }

        .summary {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #eee;
            background-color: #fdfdfd;
        }

        .summary h3 {
            margin-top: 0;
            font-size: 12px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .summary p {
            margin: 5px 0;
            display: flex;
            justify-content: space-between;
        }

        .summary span {
            font-weight: normal;
        }

        .text-right {
            text-align: right;
        }

        .profit {
            color: #28a745;
        }

        /* Green */
        .loss {
            color: #dc3545;
        }

        /* Red */
        h1,
        h2 {
            text-align: center;
            margin-bottom: 5px;
            margin-top: 0;
        }

        h1 {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 30px;
        }

        h2 {
            font-size: 14px;
            margin-bottom: 20px;
        }

        .meta {
            font-size: 9px;
            color: #555;
            margin-bottom: 15px;
            text-align: center;
        }

        hr {
            border: 0;
            border-top: 1px solid #eee;
            margin: 10px 0;
        }

        .number {
            white-space: nowrap;
        }

        /* Prevent wrapping for numbers */
        .company-logo {
            max-height: 40px;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 10px;
        }

        .company-address {
            font-size: 9px;
            color: #555;
            margin-bottom: 10px;
            text-align: left;

        }
    </style>
    <style>
        /* Styles for Signature Section */
        .signature-section {
            margin-top: 40px;
            /* Space above the signature section */
            width: 100%;
            display: table;
            /* Use table display for columns */
            table-layout: fixed;
            /* Fix column widths */
        }

        .signature-column {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 0 10px;
        }

        .signature-line {
            margin-top: 50px;
            border-bottom: 1px solid #000;
            width: 70%;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="report-title-header">
            @php
                $company = null;
                if (\Illuminate\Support\Facades\Schema::hasTable('companies')) {
                    $company = \App\Models\Company::query()->first();
                }

                $logoPath =
                    $company && $company->logo_url
                        ? \Illuminate\Support\Facades\Storage::disk('public')->path($company->logo_url)
                        : public_path('images/logomki.png');

                $logoSrc = '';
                if (file_exists($logoPath)) {
                    // Embedding as base64 is generally more reliable for DomPDF
                    $logoMime = mime_content_type($logoPath);
                    if ($logoMime) {
                        $logoSrc = 'data:' . $logoMime . ';base64,' . base64_encode(file_get_contents($logoPath));
                    }
                }
            @endphp
            @if ($logoSrc)
                <img src="{{ $logoSrc }}" alt="Logo Perusahaan" class="company-logo">
            @endif
            @if ($company)
                <p class="company-address">
                    {{ $company->address }}
                    @if ($company->city)
                        , {{ $company->city }}
                    @endif
                    @if ($company->province)
                        , {{ $company->province }}
                    @endif
                    @if ($company->postal_code)
                        {{ $company->postal_code }}
                    @endif
                </p>
                <p class="company-address">
                    {{ $company->company_name }}
                    @if ($company->email)
                        | {{ $company->email }}
                    @endif
                    @if ($company->phone)
                        | {{ $company->phone }}
                    @endif
                </p>
            @else
                <p class="company-address">Jl. Sintraman Jaya I No. 2148, 20 Ilir D II, Kec. Kemuning, Kota Palembang,
                    Sumatera Selatan 30137</p>
                <p class="company-address">
                    PT. Makna Kreatif Indonesia | maknawedding@gmail.com | +62 822-9796-2600
                </p>
            @endif
            <h1>Laporan Laba Rugi Klien</h1>
            <div class="meta">
                Dicetak pada: {{ $generatedDate }} <br>
                @if ($filterStartDate || $filterEndDate)
                    Periode Filter:
                    {{ $filterStartDate ? \Carbon\Carbon::parse($filterStartDate)->format('d M Y') : 'Awal' }} -
                    {{ $filterEndDate ? \Carbon\Carbon::parse($filterEndDate)->format('d M Y') : 'Akhir' }}
                @else
                    Periode Filter: Semua Order
                @endif
                </h1>
            </div>
        </div>
    </div>

    {{-- <h2>Detail Order</h2> --}}
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 30%;">Nama Event</th>
                <th style="width: 15%;">Tgl Closing</th>
                <th class="text-right" style="width: 12%;">Total Pemasukan</th>
                <th class="text-right" style="width: 13%;">Nilai Order (Grand Total)</th>
                <th class="text-right" style="width: 12%;">Total Pengeluaran</th>
                <th class="text-right" style="width: 13%;">Laba / Rugi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                @php
                    // Calculate sum of payments for the current order
                    // Ensure dataPembayaran relationship is loaded if dealing with many orders to avoid N+1
                    $totalPembayaranDiterimaOrder = $order->dataPembayaran()->sum('nominal');
                    $profitLoss = ($order->grand_total ?? 0) - ($order->tot_pengeluaran ?? 0);
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $order->prospect?->name_event ?? 'N/A' }}</td>
                    <td>{{ $order->closing_date ? \Carbon\Carbon::parse($order->closing_date)->format('d M Y') : '-' }}
                    </td>
                    <td class="text-right number">Rp
                        {{ number_format($totalPembayaranDiterimaOrder ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right number">Rp {{ number_format($order->grand_total ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right number">Rp {{ number_format($order->tot_pengeluaran ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="text-right number {{ $profitLoss >= 0 ? 'profit' : 'loss' }}">
                        Rp {{ number_format($profitLoss, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada data order yang ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-left"><strong>Total Keseluruhan:</strong></td>
                {{-- Colspan adjusted to 3 to cover "Order #", "Nama Event", "Tgl Closing" --}}
                <td class="text-right number"><strong>Rp {{ number_format($totalIncome, 0, ',', '.') }}</strong></td>
                {{-- $totalIncome now sums the "Total Pemasukan (Diterima)" column.
                     Controller needs to be updated if $totalIncome was previously sum of grand_total. --}}
                <td class="text-right number"><strong>Rp {{ number_format($totalExpenses, 0, ',', '.') }}</strong></td>
                {{-- $totalExpenses now sums the "Nilai Order (Grand Total)" column.
                     Controller needs to be updated if $totalExpenses was previously sum of tot_pengeluaran. --}}
                <td class="text-right number"><strong>Rp
                        {{ number_format($sumAllOrdersPengeluaran ?? 0, 0, ',', '.') }}</strong></td>
                {{-- This cell is for the sum of "Total Pengeluaran" (column 6).
                     Ensure $sumAllOrdersPengeluaran is passed from the controller. --}}
                <td class="text-right number {{ $netProfit >= 0 ? 'profit' : 'loss' }}">
                    <strong>Rp {{ number_format($netProfit, 0, ',', '.') }}</strong>
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Detail Pendapatan Lain Section -->
    @if (isset($pendapatanLain) && $pendapatanLain->isNotEmpty())
        <div style="page-break-inside: avoid; margin-top: 20px;">
            <h2 style="margin-bottom: 15px;">Detail Pendapatan Lain</h2>
            <table style="margin-bottom: 20px;">
                <thead>
                    <tr>
                        <th style="width: 10%;">Vendor</th>
                        <th style="width: 25%;">Nama Pendapatan</th>
                        <th style="width: 15%;">Tanggal</th>
                        <th style="width: 30%;">Keterangan</th>
                        <th class="text-right" style="width: 20%;">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pendapatanLain as $pendapatan)
                        <tr>
                            <td class="text-center">{{ $pendapatan->vendor->name ?? '-' }}</td>
                            <td>{{ $pendapatan->name ?? 'N/A' }}</td>
                            <td>{{ $pendapatan->tgl_bayar ? \Carbon\Carbon::parse($pendapatan->tgl_bayar)->format('d M Y') : '-' }}
                            </td>
                            <td>{{ $pendapatan->keterangan ?? '-' }}</td>
                            <td class="text-right number">Rp
                                {{ number_format($pendapatan->nominal ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="4" class="text-left"><strong>Total Pendapatan Lain:</strong></td>
                        <td class="text-right number"><strong>Rp
                                {{ number_format($totalPendapatanLain ?? 0, 0, ',', '.') }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif

    {{-- <h2>Detail Pengeluaran</h2> --}}
    @if ((isset($expenseOps) && $expenseOps->isNotEmpty()) || (isset($pengeluaranLain) && $pengeluaranLain->isNotEmpty()))
        <div style="page-break-inside: avoid; margin-top: 20px;">
            <h2 style="margin-bottom: 15px;">Detail Pengeluaran Operasional & Lainnya</h2>

            @if (isset($expenseOps) && $expenseOps->isNotEmpty())
                <h3 style="margin-bottom: 10px; color: #333;">Pengeluaran Operasional</h3>
                <table style="margin-bottom: 20px;">
                    <thead>
                        <tr>
                            <th style="width: 8%;">Vendor</th>
                            <th style="width: 30%;">Keterangan</th>
                            <th style="width: 15%;">Tanggal</th>
                            <th style="width: 20%;">Nama Pengeluaran</th>
                            <th class="text-right" style="width: 17%;">Jumlah</th>
                            <th style="width: 10%;">No. ND</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($expenseOps as $expense)
                            <tr>
                                <td class="text-center">{{ $expense->vendor->name ?? '-' }}</td>
                                <td>{{ $expense->name ?? 'N/A' }}</td>
                                <td>{{ $expense->date_expense ? \Carbon\Carbon::parse($expense->date_expense)->format('d M Y') : '-' }}
                                </td>
                                <td>{{ $expense->name ?? 'Operasional' }}</td>
                                <td class="text-right number">Rp
                                    {{ number_format($expense->amount ?? 0, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $expense->no_nd ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="4" class="text-left"><strong>Sub Total Operasional:</strong></td>
                            <td class="text-right number"><strong>Rp
                                    {{ number_format($totalExpenseOps ?? 0, 0, ',', '.') }}</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            @endif

            @if (isset($pengeluaranLain) && $pengeluaranLain->isNotEmpty())
                <h3 style="margin-bottom: 10px; color: #333;">Pengeluaran Lainnya</h3>
                <table style="margin-bottom: 20px;">
                    <thead>
                        <tr>
                            <th style="width: 8%;">Vendor</th>
                            <th style="width: 30%;">Keterangan</th>
                            <th style="width: 15%;">Tanggal</th>
                            <th style="width: 20%;">Nama Pengeluaran</th>
                            <th class="text-right" style="width: 17%;">Jumlah</th>
                            <th style="width: 10%;">No. ND</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pengeluaranLain as $expense)
                            <tr>
                                <td class="text-center">{{ $expense->vendor->name ?? '-' }}</td>
                                <td>{{ $expense->name ?? 'N/A' }}</td>
                                <td>{{ $expense->date_expense ? \Carbon\Carbon::parse($expense->date_expense)->format('d M Y') : '-' }}
                                </td>
                                <td>{{ $expense->name ?? 'Lainnya' }}</td>
                                <td class="text-right number">Rp
                                    {{ number_format($expense->amount ?? 0, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $expense->no_nd ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="4" class="text-left"><strong>Sub Total Lainnya:</strong></td>
                            <td class="text-right number"><strong>Rp
                                    {{ number_format($totalPengeluaranLain ?? 0, 0, ',', '.') }}</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            @endif

            @php
                $grandTotalExpenses = ($totalExpenseOps ?? 0) + ($totalPengeluaranLain ?? 0);
            @endphp
            <table style="margin-bottom: 20px;">
                <tfoot>
                    <tr class="total-row" style="background-color: #f0f0f0;">
                        <td colspan="4" class="text-left"><strong>TOTAL PENGELUARAN OPS & LAINNYA:</strong></td>
                        <td class="text-right number"><strong>Rp
                                {{ number_format($grandTotalExpenses, 0, ',', '.') }}</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif

    <!-- Laporan Laba Rugi -->
    @php
        // Hitung Total Pendapatan (Order + Pendapatan Lain)
        $totalPendapatanKeseluruhan = ($totalIncome ?? 0) + ($totalPendapatanLain ?? 0);

        // Hitung Total Pengeluaran (Wedding + Ops & Lain)
        $grandTotalExpenses = ($totalExpenseOps ?? 0) + ($totalPengeluaranLain ?? 0);
        $totalPengeluaranKeseluruhan = ($sumAllOrdersPengeluaran ?? 0) + $grandTotalExpenses;

        // Hitung Laba Rugi Final
        $labaRugiFinal = $totalPendapatanKeseluruhan - $totalPengeluaranKeseluruhan;
    @endphp

    <div style="page-break-inside: avoid; margin-top: 20px;">
        <h2 style="margin-bottom: 15px;">Laporan Laba Rugi</h2>
        <table style="margin-bottom: 20px;">
            <thead>
                <tr style="background-color: #e9e9e9; color: #333;">
                    <th style="width: 70%;" class="text-left"><strong>Komponen</strong></th>
                    <th style="width: 30%;" class="text-right"><strong>Jumlah</strong></th>
                </tr>
            </thead>
            <tbody>
                <!-- PENDAPATAN -->
                <tr style="background-color: #e8f5e8;">
                    <td><strong>TOTAL PENDAPATAN</strong></td>
                    <td class="text-right number"><strong>Rp
                            {{ number_format($totalPendapatanKeseluruhan, 0, ',', '.') }}</strong></td>
                </tr>
                <tr>
                    <td style="padding-left: 20px;">- Pemasukan dari Order</td>
                    <td class="text-right number">Rp {{ number_format($totalIncome ?? 0, 0, ',', '.') }}</td>
                </tr>
                @if (isset($totalPendapatanLain) && $totalPendapatanLain > 0)
                    <tr>
                        <td style="padding-left: 20px;">- Pendapatan Lain</td>
                        <td class="text-right number">Rp {{ number_format($totalPendapatanLain, 0, ',', '.') }}</td>
                    </tr>
                @endif

                <!-- PENGELUARAN -->
                <tr style="background-color: #ffe8e8;">
                    <td><strong>TOTAL PENGELUARAN</strong></td>
                    <td class="text-right number"><strong>Rp
                            {{ number_format($totalPengeluaranKeseluruhan, 0, ',', '.') }}</strong></td>
                </tr>
                <tr>
                    <td style="padding-left: 20px;">- Pengeluaran Wedding</td>
                    <td class="text-right number">Rp {{ number_format($sumAllOrdersPengeluaran ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
                @if (isset($grandTotalExpenses) && $grandTotalExpenses > 0)
                    <tr>
                        <td style="padding-left: 20px;">- Pengeluaran Ops & Lainnya</td>
                        <td class="text-right number">Rp {{ number_format($grandTotalExpenses, 0, ',', '.') }}</td>
                    </tr>
                @endif
            </tbody>
            <tfoot>
                <tr class="total-row" style="background-color: {{ $labaRugiFinal >= 0 ? '#d4edda' : '#f8d7da' }};">
                    <td><strong>LABA / RUGI BERSIH</strong></td>
                    <td class="text-right number {{ $labaRugiFinal >= 0 ? 'profit' : 'loss' }}">
                        <strong>Rp {{ number_format($labaRugiFinal, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Ringkasan Laporan Keseluruhan -->
    <div
        style="page-break-inside: avoid; margin-top: 30px; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
        <h2 style="margin-top: 0; margin-bottom: 15px;">Ringkasan Laporan</h2>
        <table style="width: 100%; border: none; margin-bottom: 0;">
            <tbody>
                <tr style="border: none;">
                    <td style="width: 50%; border: none; vertical-align: top; padding-right: 20px;">
                        <div style="font-size: 12px; line-height: 1.6;">
                            <strong>Total Pemasukan:</strong> Rp
                            {{ number_format($totalIncome ?? 0, 0, ',', '.') }}<br>
                            @if (isset($totalPendapatanLain) && $totalPendapatanLain > 0)
                                <strong>Total Pendapatan Lain:</strong> Rp
                                {{ number_format($totalPendapatanLain, 0, ',', '.') }}<br>
                            @endif
                            <strong>Total Pengeluaran Wedding:</strong> Rp
                            {{ number_format($sumAllOrdersPengeluaran ?? 0, 0, ',', '.') }}<br>
                            @if (isset($totalExpenseOps) && isset($totalPengeluaranLain))
                                <strong>Total Pengeluaran Ops & Lain:</strong> Rp
                                {{ number_format($totalExpenseOps + $totalPengeluaranLain, 0, ',', '.') }}<br>
                            @endif
                        </div>
                    </td>
                    <td style="width: 50%; border: none; vertical-align: top;">
                        <div style="font-size: 12px; line-height: 1.6;">
                            <strong>Nilai Order (Grand Total):</strong> Rp
                            {{ number_format($totalExpenses ?? 0, 0, ',', '.') }}<br>
                            <strong class="{{ ($netProfit ?? 0) >= 0 ? 'profit' : 'loss' }}">Laba Bersih
                                Wedding:</strong>
                            <span class="{{ ($netProfit ?? 0) >= 0 ? 'profit' : 'loss' }}">Rp
                                {{ number_format($netProfit ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Note: The tfoot above sums the 4th, 5th, and 7th columns.
         The sum of the 6th column (Total Pengeluaran) is not currently displayed in the tfoot. --}}
    {{-- Pastikan variabel $eventSummary dikirim dari Controller --}}
    @isset($eventSummary)
        @if (count($eventSummary) > 0)
            <h2>Ringkasan per Event</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nama Event</th>
                        <th class="text-right">Total Pemasukan</th>
                        <th class="text-right">Total Pengeluaran</th>
                        <th class="text-right">Laba / Rugi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($eventSummary as $summary)
                        <tr>
                            <td>{{ $summary['name_event'] ?? 'N/A' }}</td>
                            <td class="text-right number">Rp
                                {{ number_format($summary['total_income'] ?? 0, 0, ',', '.') }}</td>
                            <td class="text-right number">Rp
                                {{ number_format($summary['total_expenses'] ?? 0, 0, ',', '.') }}</td>
                            @php $eventProfitLoss = ($summary['total_income'] ?? 0) - ($summary['total_expenses'] ?? 0); @endphp
                            <td class="text-right number {{ $eventProfitLoss >= 0 ? 'profit' : 'loss' }}">
                                Rp {{ number_format($eventProfitLoss, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endisset

    {{-- Signature Section --}}
    <div class="signature-section">
        <div class="signature-column">
            <p>Disiapkan Oleh,</p>
            <br>
            <div class="signature-line"></div>
            <p style="margin-top: 5px;">( Finance )</p>
        </div>
        <div class="signature-column">
            <p>Disetujui Oleh,</p>
            <br>
            <div class="signature-line"></div>
            <p style="margin-top: 5px;">( Pimpinan )</p>
        </div>
    </div>

</body>

</html>
