<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Account Manager - {{ $accountManager->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #e5e5e5;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 24px;
            color: #374151;
            margin-bottom: 10px;
        }
        .report-period {
            font-size: 16px;
            color: #6b7280;
        }
        .section {
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 15px;
            border-left: 4px solid #2563eb;
            padding-left: 10px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .info-card {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        .info-label {
            font-weight: bold;
            color: #374151;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 18px;
            color: #1f2937;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stat-label {
            font-size: 14px;
            opacity: 0.9;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .table th, .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        .table th {
            background: #f3f4f6;
            font-weight: bold;
            color: #374151;
        }
        .table tr:hover {
            background: #f9fafb;
        }
        .achievement-bar {
            background: #e5e7eb;
            border-radius: 10px;
            height: 20px;
            overflow: hidden;
            margin-top: 10px;
        }
        .achievement-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #059669);
            transition: width 0.3s ease;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-achieved { background: #d1fae5; color: #065f46; }
        .status-on-track { background: #fef3c7; color: #92400e; }
        .status-behind { background: #fee2e2; color: #991b1b; }
        .currency { color: #059669; font-weight: bold; }
        .footer {
            margin-top: 40px;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        @media print {
            body { background: white; }
            .container { box-shadow: none; }
        }
    </link>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="company-name">MAKNA KREATIF REPORT</div>
            <div class="report-title">Laporan Kinerja Account Manager</div>
            <div class="report-period">Periode: {{ $monthName }} {{ $year }}</div>
        </div>

        <!-- Account Manager Info -->
        <div class="section">
            <div class="section-title">Informasi Account Manager</div>
            <div class="info-grid">
                <div class="info-card">
                    <div class="info-label">Nama</div>
                    <div class="info-value">{{ $accountManager->name }}</div>
                </div>
                <div class="info-card">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $accountManager->email }}</div>
                </div>
                <div class="info-card">
                    <div class="info-label">Periode Report</div>
                    <div class="info-value">{{ $monthName }} {{ $year }}</div>
                </div>
                <div class="info-card">
                    <div class="info-label">Tanggal Generate</div>
                    <div class="info-value">{{ date('d F Y H:i') }}</div>
                </div>
            </div>
        </div>

        <!-- Sales Performance -->
        <div class="section">
            <div class="section-title">Kinerja Penjualan</div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($totalOrders) }}</div>
                    <div class="stat-label">Total Order</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                    <div class="stat-label">Total Revenue</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">Rp {{ number_format($averageOrderValue, 0, ',', '.') }}</div>
                    <div class="stat-label">Rata-rata Order</div>
                </div>
                @if($target)
                <div class="stat-card">
                    <div class="stat-value">{{ number_format($achievementPercentage, 1) }}%</div>
                    <div class="stat-label">Pencapaian Target</div>
                </div>
                @endif
            </div>

            @if($target)
            <div class="info-card">
                <div class="info-label">Target vs Achievement</div>
                <div style="margin-top: 10px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span>Target: <span class="currency">Rp {{ number_format($target->target_amount, 0, ',', '.') }}</span></span>
                        <span>Achievement: <span class="currency">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span></span>
                    </div>
                    <div class="achievement-bar">
                        <div class="achievement-fill" style="width: {{ min($achievementPercentage, 100) }}%;"></div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Orders Detail -->
        @if($orders && $orders->count() > 0)
        <div class="section">
            <div class="section-title">Detail Order ({{ $orders->count() }} order)</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Prospect</th>
                        <th>Closing Date</th>
                        <th>Total Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $index => $order)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $order->prospect->name ?? 'N/A' }}</td>
                        <td>{{ $order->closing_date ? $order->closing_date->format('d F Y') : '-' }}</td>
                        <td class="currency">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        <td>
                            <span class="status-badge status-achieved">Closed</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Payroll Data -->
        @if($payrollData)
        <div class="section">
            <div class="section-title">Data Payroll</div>
            <div class="info-grid">
                <div class="info-card">
                    <div class="info-label">Gaji Pokok</div>
                    <div class="info-value currency">Rp {{ number_format($payrollData->gaji_pokok ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="info-card">
                    <div class="info-label">Tunjangan</div>
                    <div class="info-value currency">Rp {{ number_format($payrollData->tunjangan ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="info-card">
                    <div class="info-label">Bonus</div>
                    <div class="info-value currency">Rp {{ number_format($payrollData->bonus ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="info-card">
                    <div class="info-label">Pengurangan</div>
                    <div class="info-value currency">Rp {{ number_format($payrollData->pengurangan ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="info-card">
                    <div class="info-label">Gaji Bulanan</div>
                    <div class="info-value currency">Rp {{ number_format($payrollData->monthly_salary ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Leave Data -->
        @if($leaveData && $leaveData->count() > 0)
        <div class="section">
            <div class="section-title">Riwayat Cuti ({{ $leaveData->count() }} cuti)</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Jenis Cuti</th>
                        <th>Durasi</th>
                        <th>Alasan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaveData as $index => $leave)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $leave->start_date ? $leave->start_date->format('d F Y') : '-' }}</td>
                        <td>{{ $leave->end_date ? $leave->end_date->format('d F Y') : '-' }}</td>
                        <td>{{ $leave->leaveType->name ?? 'N/A' }}</td>
                        <td>{{ $leave->total_days ?? 0 }} hari</td>
                        <td>{{ $leave->reason ? \Illuminate\Support\Str::limit($leave->reason, 30) : '-' }}</td>
                        <td>
                            <span class="status-badge {{ $leave->status === 'approved' ? 'status-achieved' : ($leave->status === 'pending' ? 'status-on-track' : 'status-behind') }}">
                                {{ ucfirst($leave->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Summary -->
        <div class="section">
            <div class="section-title">Ringkasan Kinerja</div>
            <div class="info-card">
                <h3 style="margin: 0 0 15px 0; color: #374151;">Evaluasi Periode {{ $monthName }} {{ $year }}</h3>
                <ul style="margin: 0; padding-left: 20px; color: #6b7280;">
                    <li>Berhasil menutup {{ $totalOrders }} order dengan total revenue Rp {{ number_format($totalRevenue, 0, ',', '.') }}</li>
                    @if($target)
                    <li>Pencapaian target: {{ number_format($achievementPercentage, 1) }}% dari target Rp {{ number_format($target->target_amount, 0, ',', '.') }}</li>
                    @endif
                    <li>Rata-rata nilai per order: Rp {{ number_format($averageOrderValue, 0, ',', '.') }}</li>
                    @if($leaveData && $leaveData->count() > 0)
                    <li>Total cuti yang diambil: {{ $leaveData->count() }} kali</li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="footer">
            <p>Report ini di-generate secara otomatis dari sistem Account Manager Target</p>
            <p>PT. Makna Kreatif - {{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>