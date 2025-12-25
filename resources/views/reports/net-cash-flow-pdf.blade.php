<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $pageTitle }}</title>
    <style>
        @page {
            margin: 110px 50px 20px 60px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.2;
        }

        /* Ensure all elements use the same font */
        * {
            font-family: 'DejaVu Sans', sans-serif !important;
            box-sizing: border-box;
        }

        .header {
            position: fixed;
            top: -85px;
            left: 0;
            right: 0;
            height: 70px;
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 1px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 30px;
            text-align: center;
            font-size: 9px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 5px;
            font-style: italic;
            background-color: #fff;
        }

        /* Report Specific Styles */
        .report-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        .text-green {
            color: #000;
        }

        .text-red {
            color: #000;
        }

        .text-blue {
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            vertical-align: top;
        }

        th {
            background-color: #f3f4f6;
            font-weight: bold;
            text-align: left;
            font-size: 10px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-medium {
            font-weight: bold;
        }

        .text-xs {
            font-size: 9px;
        }

        .text-gray {
            color: #6b7280;
        }

        .mt-1 {
            margin-top: 2px;
        }

        /* Table Border Fixes */
        .no-border {
            border: none !important;
        }

        .p-0 {
            padding: 0 !important;
        }
    </style>
</head>

<body>
    <div class="footer">
        Dokumen ini diterbitkan secara otomatis oleh sistem komputer dan sah tanpa tanda tangan basah.
    </div>

    <div class="header">
        <table style="width: 100%; margin-bottom: 1px; padding-bottom: 3px;" class="no-border">
            <tr class="no-border">
                <td class="no-border p-0" style="line-height: 1; text-align: left;">
                    <div style="font-size: 14px; font-weight: bold; text-transform: uppercase;">PT. Makna Kreatif
                        Indonesia</div>
                    <div style="font-size: 12px;">
                        Alamat : Jln. Sintraman Jaya, No. 2148, Sekip Jaya, Palembang<br>
                        No. Tlp : +62 822-9796-2600<br>
                        Email : maknawedding@gmail.com
                    </div>
                </td>
                <td class="no-border p-0" style="width: 40%; text-align: right; vertical-align: middle;">
                    @php
                        $logoPath = public_path(config('invoice.logo', 'images/logo.png'));
                        if (file_exists($logoPath)) {
                            $logoType = pathinfo($logoPath, PATHINFO_EXTENSION);
                            $logoData = file_get_contents($logoPath);
                            $logoBase64 = 'data:image/' . $logoType . ';base64,' . base64_encode($logoData);
                        } else {
                            $logoBase64 = '';
                        }
                    @endphp
                    @if ($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Company Logo" style="max-height: 50px; width: auto;">
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="report-title">
        {{ $pageTitle }}
        <div style="font-size: 11px; font-weight: normal; margin-top: 5px; text-transform: none; color: #555;">
            Digenerate pada: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY, HH:mm') }}
        </div>
    </div>

    <table style="width: 100%; margin-bottom: 20px; border: none;">
        <tr>
            <td style="width: 32%; border: 1px solid #000; background-color: #fff; padding: 10px;">
                <h3 style="color: #000; font-size: 10px; text-transform: uppercase; margin: 0 0 5px 0;">Total Pembayaran
                    Masuk</h3>
                <p style="font-size: 14px; font-weight: bold; margin: 0; color: #000;">
                    {{ number_format($totalPaymentsAll, 0, ',', '.') }}</p>
            </td>
            <td style="width: 2%; border: none;"></td>
            <td style="width: 32%; border: 1px solid #000; background-color: #fff; padding: 10px;">
                <h3 style="color: #000; font-size: 10px; text-transform: uppercase; margin: 0 0 5px 0;">Total
                    Pengeluaran Project</h3>
                <p style="font-size: 14px; font-weight: bold; margin: 0; color: #000;">
                    {{ number_format($totalExpensesAll, 0, ',', '.') }}</p>
            </td>
            <td style="width: 2%; border: none;"></td>
            <td style="width: 32%; border: 1px solid #000; background-color: #fff; padding: 10px;">
                <h3 style="color: #000; font-size: 10px; text-transform: uppercase; margin: 0 0 5px 0;">Net Cash Flow
                    (Sisa)</h3>
                <p style="font-size: 14px; font-weight: bold; margin: 0; color: #000;">
                    {{ number_format($totalNetCashFlowAll, 0, ',', '.') }}</p>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th style="width: 25px;" class="text-center">No</th>
                <th>Nama Project / Order</th>
                <th>Account Manager</th>
                <th>Event Manager</th>
                <th class="text-right">Pembayaran Masuk</th>
                <th class="text-right">Pengeluaran</th>
                <th class="text-right">Net Cash Flow</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $index => $order)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        <div class="font-medium">{{ $order->name }}</div>
                        <div class="text-gray text-xs">
                            {{ ucwords(strtolower($order->items->first()?->product?->parent?->name ?? ($order->items->first()?->product?->name ?? '-'))) }}
                        </div>
                        <div class="text-gray-500 text-xs font-bold mt-1">
                            Total Paket : {{ number_format($order->grand_total, 0, ',', '.') }}
                        </div>
                    </td>
                    <td>
                        {{ $order->user->name ?? '-' }}
                        <div class="text-gray text-xs mt-1">
                            Closing {{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}
                        </div>
                    </td>
                    <td>{{ $order->employee->name ?? '-' }}</td>
                    <td class="text-right text-gray-900 font-medium">
                        {{ number_format($order->total_payments_received, 0, ',', '.') }}
                    </td>
                    <td class="text-right text-gray-900 font-medium">
                        {{ number_format($order->total_expenses_incurred, 0, ',', '.') }}
                    </td>
                    <td class="text-right font-medium {{ $order->net_cash_flow >= 0 ? 'text-blue' : 'text-red' }}">
                        {{ number_format($order->net_cash_flow, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-gray" style="padding: 20px;">
                        Tidak ada data order dengan status ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #e5e7eb; font-weight: bold;">
                <td colspan="4" class="text-right">TOTAL</td>
                <td class="text-right text-green">
                    {{ number_format($totalPaymentsAll, 0, ',', '.') }}
                </td>
                <td class="text-right text-red">
                    {{ number_format($totalExpensesAll, 0, ',', '.') }}
                </td>
                <td class="text-right text-blue">
                    {{ number_format($totalNetCashFlowAll, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
