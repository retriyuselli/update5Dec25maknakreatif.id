<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview PDF - Surat Persetujuan {{ $notaDinas->no_nd }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                margin: 0;
                padding: 3px;
            }
        }

        .pdf-container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 0px rgba(0, 0, 0, 0.1);
            min-height: 297mm;
        }

        .content {
            padding: 20px;
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }

        th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-transform: uppercase;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .header {
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .section {
            margin-bottom: 20px;
        }

        .section h3 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }

        .company-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .company-left,
        .company-right {
            width: 48%;
        }

        .approval-section {
            border-top: 1px solid #ccc;
            padding-top: 20px;
            margin-top: 20px;
        }

        .approval-grid {
            display: flex;
            justify-content: space-between;
        }

        .approval-item {
            width: 23%;
            text-align: center;
            padding: 0 5px;
        }

        .signature-space {
            height: 60px;
            border-bottom: 1px solid #333;
            margin: 15px 0;
        }

        .btn-group {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            gap: 10px;
        }
    </style>
</head>

<body class="bg-gray-100">
    <!-- Action Buttons -->
    <div class="btn-group no-print">
        
        <button onclick="window.print()"
            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg shadow-lg transition-colors">
            🖨️ Print
        </button>
        <a href="{{ url()->previous() }}"
            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-lg transition-colors">
            ← Kembali
        </a>
    </div>

    <div class="pdf-container">
        <div class="content">
            <!-- Header -->
            <div class="header">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 style="font-size: 18px; font-weight: bold; margin-bottom: 5px;">SURAT PERSETUJUAN PEMBAYARAN
                        </h1>
                        <h2 style="font-size: 16px; font-weight: bold; margin-bottom: 5px;">{{ $notaDinas->no_nd }}</h2>
                        <p style="font-size: 11px; color: #666;">Tgl: {{ $notaDinas->created_at->format('d F Y') }}
                        </p>
                    </div>
                    <div>
                        <img src="{{ asset('images/logomkiinv.png') }}" alt="Logo" style="height: 30px;">
                    </div>
                </div>
            </div>

            <!-- Company Info -->
            <div class="company-info">
                <div class="company-left">
                    <h3>Diajukan oleh</h3>
                    <p>Nama: <strong>{{ $notaDinas->pengirim->name ?? 'N/A' }}</strong></p>
                    <p>Status: <strong>{{ ucfirst($notaDinas->status) }}</strong></p>
                </div>
                <div class="company-right">
                    <h3>Informasi Nota Dinas</h3>
                    <p><strong>Sifat:</strong> {{ $notaDinas->sifat }}</p>
                    <p><strong>Hal:</strong> {{ $notaDinas->hal }}</p>
                    @if ($notaDinas->nd_upload)
                        <p><strong>File Lampiran:</strong> {{ basename($notaDinas->nd_upload) }}</p>
                    @endif
                </div>
            </div>

            <!-- Detail Pengeluaran -->
            <div class="section">
                <h3>Detail Pengeluaran</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Vendor</th>
                            <th>Keperluan</th>
                            <th>Event</th>
                            <th>Invoice</th>
                            <th class="text-right">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($details as $detail)
                            <tr>
                                <td>
                                    <strong>{{ $detail->vendor->name ?? 'N/A' }}</strong>
                                    {{-- @if ($detail->order && $detail->order->prospect)
                                        <br><small>{{ $detail->order->prospect->name ?? '' }}</small>
                                    @endif --}}
                                    @if ($detail->payment_stage)
                                        <br><small style="color: #1e40af; font-weight: bold;">Tahap:
                                            {{ $detail->payment_stage }}</small>
                                    @endif
                                </td>
                                <td>{{ $detail->keperluan }}</td>
                                <td>
                                    {{ $detail->order && $detail->order->prospect ? $detail->order->prospect->name_event : $detail->event ?? '-' }}
                                    @if ($detail->jenis_pengeluaran)
                                        <br><small
                                            style="color: #059669; font-weight: bold;">{{ ucfirst($detail->jenis_pengeluaran) }}</small>
                                    @endif
                                </td>
                                <td>
                                    {{ $detail->invoice_number ?? '-' }}
                                    @if ($detail->status_invoice)
                                        <br><small>({{ ucfirst($detail->status_invoice) }})</small>
                                    @endif
                                    @if ($detail->invoice_file)
                                        <br><small style="color: #059669; font-weight: bold;">📎 File Ada</small>
                                    @else
                                        <br><small style="color: #dc2626;">📄 Tidak Ada File</small>
                                    @endif
                                </td>
                                <td class="text-right font-bold">
                                     {{ number_format($detail->jumlah_transfer, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #f8f9fa;">
                            <td colspan="4" class="text-right font-bold">Total:</td>
                            <td class="text-right font-bold">
                                 {{ number_format($totalJumlahTransfer, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Detail Transfer Bank -->
            <div class="section">
                <h3>Detail Transfer Bank</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Bank</th>
                            <th>No. Rekening</th>
                            <th>Atas Nama</th>
                            <th>Vendor</th>
                            <th class="text-right">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $bankGroups = $details->whereNotNull('bank_name')->groupBy(function ($item) {
                                return $item->bank_name . '|' . $item->bank_account;
                            });
                        @endphp
                        @foreach ($bankGroups as $bankGroup)
                            @php $firstDetail = $bankGroup->first(); @endphp
                            <tr>
                                <td><strong>{{ $firstDetail->bank_name }}</strong></td>
                                <td>{{ $firstDetail->bank_account }}</td>
                                <td>{{ $firstDetail->account_holder }}</td>
                                <td>
                                    {{ $firstDetail->vendor->name ?? 'N/A' }}
                                    @if ($bankGroup->count() > 1)
                                        <br><small>+ {{ $bankGroup->count() - 1 }} vendor lainnya</small>
                                    @endif
                                </td>
                                <td class="text-right font-bold">
                                     {{ number_format($bankGroup->sum('jumlah_transfer'), 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #f8f9fa;">
                            <td colspan="4" class="text-right font-bold">Total Transfer:</td>
                            <td class="text-right font-bold">
                                @php
                                    $totalBankTransfer = $details->whereNotNull('bank_name')->sum('jumlah_transfer');
                                @endphp
                                 {{ number_format($totalBankTransfer, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Signature Section -->
            <div class="approval-section">
                <h3>Persetujuan dan Tanda Tangan</h3>
                <div class="approval-grid" style="margin-top: 20px;">
                    <div class="approval-item">
                        <p><strong>Admin</strong></p>
                        <div class="signature-space"></div>
                        <p><strong>{{ $notaDinas->pengirim->name ?? 'N/A' }}</strong></p>
                        <p>Tgl: {{ $notaDinas->created_at->format('d/m/Y') }}</p>
                    </div>

                    <div class="approval-item">
                        <p><strong>Event Manager</strong></p>
                        <div class="signature-space"></div>
                        <p><strong>_________________</strong></p>
                        <p>Tgl: ___________</p>
                    </div>

                    <div class="approval-item">
                        <p><strong>Finance</strong></p>
                        <div class="signature-space"></div>
                        <p><strong>{{ $notaDinas->penerima->name ?? 'Finance' }}</strong></p>
                        <p>Tgl: ___________</p>
                    </div>

                    <div class="approval-item">
                        <p><strong>Pimpinan</strong></p>
                        <div class="signature-space"></div>
                        <p><strong>{{ $notaDinas->approver->name ?? 'Belum Disetujui' }}</strong></p>
                        <p>{{ $notaDinas->approved_at ? $notaDinas->approved_at->format('d/m/Y') : 'Tgl: ___________' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer mt-8 pt-6 border-t border-gray-300 text-center">
        <p class="text-xs text-gray-600" style="font-family: 'Noto Sans', sans-serif;">Catatan Penting!!!</p>
        <p class="text-xs text-gray-600" style="font-family: 'Noto Sans', sans-serif;">Pastikan semua dokumen pendukung
            telah dilampirkan sebelum mengajukan nota dinas.<br> Jangan sampai ada kesalahan sebelum mengirimkan nota
            dinas.</p>
    </div>

    <script>
        // Add keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
            if (e.key === 'Escape') {
                history.back();
            }
        });
    </script>
</body>

</html>
