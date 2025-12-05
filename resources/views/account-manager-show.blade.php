<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Laporan Account Manager - {{ $accountManager->name ?? 'Unknown' }} - {{ $monthName ?? 'Unknown Month' }} {{ $year ?? 'Unknown Year' }}</title>
    <meta name="author" content="Makna Kreatif">
    <meta name="description" content="Laporan Kinerja Account Manager">
    <meta name="keywords" content="Account Manager, Report, Performance, Makna Kreatif" />
    <meta name="robots" content="INDEX,FOLLOW">

    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Favicons - Makna Kreatif Favicon -->
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('images/favicon_makna.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('images/favicon_makna.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('images/favicon_makna.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('images/favicon_makna.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('images/favicon_makna.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('images/favicon_makna.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('images/favicon_makna.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('images/favicon_makna.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon_makna.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/favicon_makna.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon_makna.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/favicon_makna.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon_makna.png') }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon_makna.png') }}" type="image/png">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('images/favicon_makna.png') }}">
    <meta name="theme-color" content="#ffffff">

    <!--==============================
	  Google Fonts
	============================== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">


    <!--==============================
	    All CSS File
	============================== -->
    <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('assets_am/css/bootstrap.min.css') }}">
    <!-- Theme Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets_am/css/style.css') }}">
    
    <!-- Custom Noto Sans Font CSS -->
    <style>
        * {
            font-family: 'Noto Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
        }
        
        body {
            font-family: 'Noto Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Noto Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-weight: 600;
        }
        
        .invoice-table th,
        .invoice-table td {
            font-family: 'Noto Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        
        .table-title {
            font-family: 'Noto Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-weight: 600;
        }
        
        .company-address {
            font-family: 'Noto Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        
        .invoice-note {
            font-family: 'Noto Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        
        .motivational-section h4,
        .motivational-section p,
        .motivational-footer h4,
        .motivational-footer p {
            font-family: 'Noto Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        
        .invoice-left b,
        .invoice-right b {
            font-family: 'Noto Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-weight: 600;
        }
        
        .total-table th,
        .total-table td,
        .total-table2 th,
        .total-table2 td {
            font-family: 'Noto Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        /* Print Styles for A4 */
    </style>
</head>

<body>


    <!--[if lte IE 9]>
    	<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
  <![endif]-->


    <!--********************************
   		Code Start From Here 
	******************************** -->

    <div class="invoice-container-wrap">
        <div class="invoice-container">
            <main>
                <!--==============================
Invoice Area
==============================-->
                <div class="themeholy-invoice invoice_style6">
                    <div class="download-inner" id="download_section">
                        <!--==============================
	Header Area
==============================-->
                        <header class="themeholy-header header-layout4">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-auto">
                                    <div class="header-logo">
                                        <a href="#"><img src="{{ asset('images/logomki.png') }}" alt="Makna Kreatif" width="250" height="auto" style="max-width: 250px; height: auto;"></a>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <h1 class="big-title">Laporan Account Manager</h1>
                                    <span><b>Periode: </b> {{ $monthName ?? 'Unknown Month' }} {{ $year ?? 'Unknown Year' }}</span>
                                </div>
                            </div>
                        </header>
                        <div class="row justify-content-between mb-4">
                            <div class="col-auto">
                                <div class="invoice-left">
                                    <b>Account Manager:</b>
                                    <address>
                                        <strong>{{ $accountManager->name ?? 'Unknown' }}</strong><br>
                                        Email: {{ $accountManager->email ?? 'No email' }}<br>
                                        @if($reportData['target'] ?? null)
                                        Target:  {{ number_format($reportData['target']->target_amount ?? 0, 0, ',', '.') }}<br>
                                        Status: {{ ucfirst($reportData['target']->status ?? 'pending') }}
                                        @else
                                        Target: Tidak ada target yang ditetapkan
                                        @endif
                                    </address>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="invoice-right">
                                    <b>PT. Makna Kreatif Indonesia</b>
                                    <address>
                                        Jl. Sintraman Jaya I No. 2148 <br>
                                        20 Ilir D II, Kec. Kemuning, Kota Palembang<br>
                                        Sumatera Selatan 30137<br>
                                        Email: info@maknawedding.id<br>
                                        Tlp: +62 813 7318 3794
                                    </address>
                                </div>
                            </div>
                        </div>
                        <hr class="style1">

                        <!-- Target vs Achievement Section -->
                        <div class="target-achievement-section" style="background: #F5F5F5; border-radius: 15px; padding: 25px; margin: 20px 0;">
                            <h4 style="color: #333; font-weight: bold; margin-bottom: 20px;">Target vs Achievement</h4>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                <div>
                                    <span style="color: #666; font-size: 1rem;">Target: </span>
                                    <span style="color: #4F7FFF; font-weight: bold; font-size: 1.2rem;"> {{ number_format($reportData['target']->target_amount ?? 0, 0, '.', '.') }}</span>
                                </div>
                                <div style="text-align: right;">
                                    <span style="color: #666; font-size: 1rem;">Achievement: </span>
                                    <span style="color: #4F7FFF; font-weight: bold; font-size: 1.2rem;"> {{ number_format($reportData['totalRevenue'] ?? 0, 0, '.', '.') }}</span>
                                </div>
                            </div>
                            
                            <!-- Progress Bar -->
                            <div class="progress-bar-container" style="background: #E0E0E0; border-radius: 10px; height: 20px; overflow: hidden; margin: 15px 0;">
                                <div style="background: linear-gradient(90deg, #4F7FFF 0%, #6B9BFF 100%); height: 100%; width: {{ min($reportData['achievementPercentage'] ?? 0, 100) }}%; border-radius: 10px; transition: width 0.5s ease;"></div>
                            </div>
                        </div>

                        <p class="table-title"><b>Ringkasan Kinerja:</b></p>
                        <table class="invoice-table table-style1 mt-2">
                            <thead>
                                <tr>
                                    <th>Metrik</th>
                                    <th>Target</th>
                                    <th>Realisasi</th>
                                    <th>Persentase</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Revenue</strong></td>
                                    <td> {{ number_format($reportData['target']->target_amount ?? 0, 0, ',', '.') }}</td>
                                    <td> {{ number_format($reportData['totalRevenue'] ?? 0, 0, ',', '.') }}</td>
                                    <td> {{ number_format($reportData['achievementPercentage'] ?? 0, 1) }}%</td>
                                    <td>
                                        @if(($reportData['achievementPercentage'] ?? 0) >= 100)
                                            <span style="color: green;">✓ Tercapai</span>
                                        @elseif(($reportData['achievementPercentage'] ?? 0) >= 75)
                                            <span style="color: orange;">⚠ Hampir Tercapai</span>
                                        @else
                                            <span style="color: red;">✗ Belum Tercapai</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Jumlah Order</strong></td>
                                    <td>-</td>
                                    <td>{{ $reportData['totalOrders'] ?? 0 }} orders</td>
                                    <td>-</td>
                                    <td>
                                        @if(($reportData['totalOrders'] ?? 0) > 0)
                                            <span style="color: green;">✓ Ada Aktivitas</span>
                                        @else
                                            <span style="color: red;">✗ Tidak Ada Order</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Rata-rata Order</strong></td>
                                    <td>-</td>
                                    <td> {{ number_format($reportData['averageOrderValue'] ?? 0, 0, ',', '.') }}</td>
                                    <td>-</td>
                                    <td>
                                        @if(($reportData['averageOrderValue'] ?? 0) > 800000000)
                                            <span style="color: green;">✓ Tinggi</span>
                                        @elseif(($reportData['averageOrderValue'] ?? 0) > 500000000)
                                            <span style="color: orange;">⚠ Sedang</span>
                                        @else
                                            <span style="color: red;">✗ Rendah</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        @if(!empty($reportData['orders']) && count($reportData['orders']) > 0)
                        <p class="table-title"><b>Detail Project:</b></p>
                        <table class="invoice-table table-style1 mt-2">
                            <thead>
                                <tr>
                                    {{-- <th>No</th> --}}
                                    {{-- <th>Order ID</th> --}}
                                    <th>Client</th>
                                    <th>Package</th>
                                    <th>Tanggal</th>
                                    <th>Grand Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reportData['orders'] as $index => $order)
                                <tr>
                                    {{-- <td>{{ $index + 1 }}</td> --}}
                                    {{-- <td>#{{ $order->order_number ?? $order->id }}</td> --}}
                                    <td>{{ $order->prospect->name_event ?? 'N/A' }}</td>
                                    <td>
                                        @if(isset($order->package_name) && !empty($order->package_name))
                                            {{ $order->package_name }}
                                        @elseif(isset($order->items) && $order->items->isNotEmpty())
                                            @php
                                                $firstItem = $order->items->first();
                                            @endphp
                                            {{ $firstItem->product->name ?? 'Custom Package' }}
                                            @if($order->items->count() > 1)
                                                <small>(+{{ $order->items->count() - 1 }} more)</small>
                                            @endif
                                        @else
                                            Custom Package
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y') }}</td>
                                    <td style="text-align: right;"> {{ number_format($order->grand_total ?? 0, 0, ',', '.') }}</td>
                                    <td>
                                        @if($order->status == 'confirmed' || (is_object($order->status) && $order->status->value == 'confirmed'))
                                            <span style="color: green;">✓ Confirmed</span>
                                        @elseif($order->status == 'pending' || (is_object($order->status) && $order->status->value == 'pending'))
                                            <span style="color: orange;">⏳ Pending</span>
                                        @else
                                            <span style="color: blue;">{{ is_object($order->status) ? ucfirst($order->status->value) : ucfirst($order->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="no-orders-section" style="text-align: center; padding: 40px; background-color: #f8f9fa; border-radius: 8px; margin: 20px 0;">
                            <h4 style="color: #6c757d;">Belum Ada Order</h4>
                            <p style="color: #6c757d; margin: 0;">Tidak ada order pada periode ini. Semangat untuk mendapatkan order pertama!</p>
                        </div>
                        @endif

                        <!-- Detail Tahun Berjalan Section -->
                        <p class="table-title"><b>Detail Tahun Berjalan ({{ $year }}):</b></p>
                        <table class="invoice-table table-style1 mt-2">
                            <thead>
                                <tr>
                                    <th>Bulan</th>
                                    <th>Total Order</th>
                                    <th>Revenue</th>
                                    <th>Target Bulanan</th>
                                    <th>Achievement</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $months = [
                                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                    ];
                                    
                                    $currentMonth = (int) date('n');
                                    $fixedTargetAmount = 1000000000; // Target tetap tidak berubah
                                    
                                    // Get user ID for actual data queries
                                    $userId = $accountManager->id ?? 1;
                                    
                                    // Query data aktual untuk setiap bulan dari database
                                    $actualYearlyData = [];
                                    
                                    for($month = 1; $month <= 12; $month++) {
                                        if($month <= $currentMonth) {
                                            // Query orders menggunakan kriteria yang sama dengan AccountManagerTargetResource
                                            // Menggunakan closing_date dan total_price sesuai dengan sistem yang ada
                                            $monthlyOrders = \App\Models\Order::where('user_id', $userId)
                                                ->whereNotNull('closing_date')
                                                ->whereYear('closing_date', $year)
                                                ->whereMonth('closing_date', $month)
                                                ->get();
                                                
                                            // Hitung total revenue menggunakan total_price (sesuai AccountManagerTarget)
                                            $monthlyRevenue = $monthlyOrders->sum('total_price') ?? 0;
                                            $monthlyOrderCount = $monthlyOrders->count();
                                            
                                        } else {
                                            // Bulan yang belum terjadi
                                            $monthlyRevenue = 0;
                                            $monthlyOrderCount = 0;
                                        }
                                        
                                        $actualYearlyData[$month] = [
                                            'name' => $months[$month],
                                            'orders' => $monthlyOrderCount,
                                            'revenue' => $monthlyRevenue,
                                            'target' => $fixedTargetAmount
                                        ];
                                    }
                                    
                                    // Gunakan data aktual
                                    $fixedYearlyData = $actualYearlyData;
                                    
                                    $totalYearlyOrders = 0;
                                    $totalYearlyRevenue = 0;
                                    $totalYearlyTarget = 0;
                                    
                                    // Hitung total hanya sampai bulan berjalan
                                    for($month = 1; $month <= $currentMonth; $month++) {
                                        $totalYearlyOrders += $fixedYearlyData[$month]['orders'];
                                        $totalYearlyRevenue += $fixedYearlyData[$month]['revenue'];
                                        $totalYearlyTarget += $fixedYearlyData[$month]['target'];
                                    }
                                    
                                    // Calculate achievement for each month
                                    foreach($fixedYearlyData as $key => $data) {
                                        $fixedYearlyData[$key]['achievement'] = $data['target'] > 0 ? ($data['revenue'] / $data['target']) * 100 : 0;
                                    }
                                @endphp
                                
                                @foreach($fixedYearlyData as $month => $data)
                                    @if($month <= $currentMonth)
                                    <tr>
                                        <td>
                                            <strong>{{ $data['name'] }}</strong>
                                            @if($month == $currentMonth)
                                                <small style="color: #4F7FFF;"> (Bulan Ini)</small>
                                            @endif
                                        </td>
                                        <td>{{ number_format($data['orders']) }} orders</td>
                                        <td> {{ number_format($data['revenue'], 0, ',', '.') }}</td>
                                        <td> {{ number_format($data['target'], 0, ',', '.') }}</td>
                                        <td>{{ number_format($data['achievement'], 1) }}%</td>
                                        <td>
                                            @if($data['achievement'] >= 100)
                                                <span style="color: green;">✓ Tercapai</span>
                                            @elseif($data['achievement'] >= 75)
                                                <span style="color: orange;">⚠ Hampir Tercapai</span>
                                            @else
                                                <span style="color: red;">✗ Belum Tercapai</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                                
                                <!-- Summary Row -->
                                <tr style="background-color: #ffffff; color: rgb(0, 0, 0); font-weight: bold;">
                                    <td><strong>TOTAL TAHUN {{ $year }}</strong></td>
                                    <td><strong>{{ number_format($totalYearlyOrders) }} orders</strong></td>
                                    <td><strong> {{ number_format($totalYearlyRevenue, 0, ',', '.') }}</strong></td>
                                    <td><strong> {{ number_format($totalYearlyTarget, 0, ',', '.') }}</strong></td>
                                    <td><strong>{{ $totalYearlyTarget > 0 ? number_format(($totalYearlyRevenue / $totalYearlyTarget) * 100, 1) : '0.0' }}%</strong></td>
                                    <td>
                                        @php
                                            $yearlyAchievement = $totalYearlyTarget > 0 ? ($totalYearlyRevenue / $totalYearlyTarget) * 100 : 0;
                                        @endphp
                                        @if($yearlyAchievement >= 100)
                                            <span style="color: #90EE90;">✓ TERCAPAI</span>
                                        @elseif($yearlyAchievement >= 75)
                                            <span style="color: #FFD700;">⚠ HAMPIR TERCAPAI</span>
                                        @else
                                            <span style="color: #c40623;">✗ BELUM TERCAPAI</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <!-- Yearly Performance Insight -->
                        <div class="yearly-performance-insight decorative-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; padding: 20px; color: white; text-align: center; margin: 20px 0;">
                            @php
                                $avgMonthlyAchievement = $currentMonth > 0 ? $yearlyAchievement / $currentMonth : 0;
                                $projectedYearlyRevenue = $avgMonthlyAchievement > 0 ? ($totalYearlyRevenue / $currentMonth) * 12 : 0;
                            @endphp
                            <h4 style="color: white; margin-bottom: 10px;">� Proyeksi Tahun {{ $year }}</h4>
                            <p style="color: white; margin: 0;">
                                Berdasarkan performa {{ $currentMonth }} bulan terakhir, proyeksi revenue akhir tahun: 
                                <strong> {{ number_format($projectedYearlyRevenue, 0, ',', '.') }}</strong>
                            </p>
                            <p style="color: white; margin: 5px 0 0 0; font-size: 0.9em;">
                                @if($projectedYearlyRevenue >= ($fixedTargetAmount * 12))
                                    🎯 Proyeksi menunjukkan target tahunan akan tercapai!
                                @else
                                    💪 Butuh akselerasi untuk mencapai target tahunan!
                                @endif
                            </p>
                        </div>

                        <table class="invoice-table table-style1 mt-4">
                            <thead>
                                <tr>
                                    {{-- <td><b>Periode: </b> {{ \Carbon\Carbon::parse($reportData['target']->start_date ?? now())->format('F Y') }}</td> --}}
                                    <td><b>Generated: </b> {{ now()->format('d/m/Y H:i') }}</td>
                                    <td><b>System: </b> Makna Kreatif CRM</td>
                                </tr>
                            </thead>
                            {{-- <tbody>
                                <tr>
                                    <td colspan="3"><b>Catatan: </b> Laporan ini menampilkan kinerja Account Manager berdasarkan target yang ditetapkan untuk periode yang sedang berjalan.</td>
                                </tr>
                            </tbody> --}}
                        </table>
                                    {{-- <td><b>Child</b></td>
                                    <td>0</td>
                                </tr>
                            </tbody> --}}
                        </table>
                        <div class="row justify-content-between">
                            <div class="col-auto">
                                <div class="invoice-left tips-section">
                                    <b>Tips Sukses Account Manager</b>
                                    <p class="mb-0">1. Follow up dengan client secara berkala <br> 
                                    2. Berikan solusi yang sesuai kebutuhan <br>
                                    3. Jaga hubungan baik dengan semua vendor <br>
                                    4. Pahami produk dan layanan <br>
                                    5. Dengarkan kebutuhan dan keluhan client <br>
                                    6. Kelola waktu dan prioritas dengan baik <br>
                                    7. Bangun komunikasi yang jelas dan transparan <br>
                                    8. Selalu update tren industri dan kompetitor <br>
                                    9. Buat laporan progress yang terukur <br>
                                    10. Jaga profesionalisme dan integritas 
                                </p>                                    
                                </div>
                            </div>
                            <div class="col-auto">
                                <table class="total-table">
                                    <tr>
                                        <th>Target Bulanan:</th>
                                        <td> {{ number_format($reportData['target']->target_amount ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Terealisasi:</th>
                                        <td> {{ number_format($reportData['totalRevenue'] ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Persentase:</th>
                                        <td>{{ number_format($reportData['achievementPercentage'] ?? 0, 1) }}%</td>
                                    </tr>
                                    <tr style="background-color: #f8f9fa;">
                                        <th>Status Target:</th>
                                        <td>
                                            @if(($reportData['achievementPercentage'] ?? 0) >= 100)
                                                <span style="color: green; font-weight: bold;">✓ TERCAPAI</span>
                                            @elseif(($reportData['achievementPercentage'] ?? 0) >= 75)
                                                <span style="color: orange; font-weight: bold;">⚠ HAMPIR TERCAPAI</span>
                                            @else
                                                <span style="color: red; font-weight: bold;">✗ BELUM TERCAPAI</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="motivational-footer" style="background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%); border-radius: 10px; padding: 20px; color: white; text-align: center; margin: 20px 0;">
                            @if(($reportData['achievementPercentage'] ?? 0) >= 100)
                                <h4 style="color: white; margin-bottom: 10px;">🏆 EXCELLENT PERFORMANCE!</h4>
                                <p style="color: white; margin: 0;">Target tercapai dengan sempurna! Anda adalah inspirasi untuk tim lainnya. Pertahankan prestasi gemilang ini!</p>
                            @elseif(($reportData['achievementPercentage'] ?? 0) >= 75)
                                <h4 style="color: white; margin-bottom: 10px;">🎯 KEEP PUSHING!</h4>
                                <p style="color: white; margin: 0;">Hanya tinggal sedikit lagi untuk mencapai target. Konsistensi adalah kunci kesuksesan!</p>
                            @else
                                <h4 style="color: white; margin-bottom: 10px;">💪 NEVER GIVE UP!</h4>
                                <p style="color: white; margin: 0;">Setiap tantangan adalah kesempatan untuk berkembang. Tetap semangat dan pantang menyerah!</p>
                            @endif
                        </div>
                        
                        <!-- Employee Information Section -->
          
                                <!-- Payroll Information -->
                                                    
                                <!-- Leave Balance Information -->
                            
                            <!-- Additional Benefits Info -->
                        
                        <!-- Signature Section -->
                        <div class="signature-section" style="margin: 40px 0; padding: 30px 0; border-top: 2px solid #eee;">
                            <div class="row justify-content-between">
                                <!-- Account Manager Signature -->
                                <div class="col-md-5 text-center">
                                    <p style="margin-bottom: 5px; font-weight: 600; color: #333;">Account Manager</p>
                                    <div style="height: 80px; border-bottom: 1px solid #ccc; margin: 20px 0; position: relative;">
                                        <!-- Space for manual signature -->
                                        <div style="position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%); font-size: 12px; color: #888;">
                                            (Tanda Tangan)
                                        </div>
                                    </div>
                                    <p style="margin: 10px 0 5px 0; font-weight: 600; color: #333;">{{ $accountManager->name ?? 'Unknown' }}</p>
                                    <p style="margin: 0; font-size: 12px; color: #666;">
                                        Tanggal: {{ now()->format('d/m/Y') }}
                                    </p>
                                </div>
                                
                                <!-- Direktur Signature -->
                                <div class="col-md-5 text-center">
                                    <p style="margin-bottom: 5px; font-weight: 600; color: #333;">Direktur</p>
                                    <div style="height: 80px; border-bottom: 1px solid #ccc; margin: 20px 0; position: relative;">
                                        <!-- Space for manual signature -->
                                        <div style="position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%); font-size: 12px; color: #888;">
                                            (Tanda Tangan)
                                        </div>
                                    </div>
                                    <p style="margin: 10px 0 5px 0; font-weight: 600; color: #333;">Rama Dhona Utama</p>
                                    <p style="margin: 0; font-size: 12px; color: #666;">
                                        PT. Makna Kreatif Indonesia
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <p class="invoice-note mt-3">
                            <svg width="14" height="18" viewBox="0 0 14 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3.64581 13.7917H10.3541V12.5417H3.64581V13.7917ZM3.64581 10.25H10.3541V9.00002H3.64581V10.25ZM1.58331 17.3334C1.24998 17.3334 0.958313 17.2084 0.708313 16.9584C0.458313 16.7084 0.333313 16.4167 0.333313 16.0834V1.91669C0.333313 1.58335 0.458313 1.29169 0.708313 1.04169C0.958313 0.791687 1.24998 0.666687 1.58331 0.666687H9.10415L13.6666 5.22919V16.0834C13.6666 16.4167 13.5416 16.7084 13.2916 16.9584C13.0416 17.2084 12.75 17.3334 12.4166 17.3334H1.58331ZM8.47915 5.79169V1.91669H1.58331V16.0834H12.4166V5.79169H8.47915ZM1.58331 1.91669V5.79169V1.91669V16.0834V1.91669Z" fill="#2D7CFE" />
                            </svg>

                            <b>CATATAN: </b>Laporan ini telah diverifikasi oleh Account Manager dan disetujui oleh Direktur PT. Makna Kreatif Indonesia sebagai dokumen resmi evaluasi kinerja periode {{ $monthName ?? 'Unknown Month' }} {{ $year ?? 'Unknown Year' }}.
                        </p>
                    </div>
                    <div class="invoice-buttons">
                        <button class="print_btn">
                            <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16.25 13H3.75C3.38542 13 3.08594 13.1172 2.85156 13.3516C2.61719 13.5859 2.5 13.8854 2.5 14.25V19.25C2.5 19.6146 2.61719 19.9141 2.85156 20.1484C3.08594 20.3828 3.38542 20.5 3.75 20.5H16.25C16.6146 20.5 16.9141 20.3828 17.1484 20.1484C17.3828 19.9141 17.5 19.6146 17.5 19.25V14.25C17.5 13.8854 17.3828 13.5859 17.1484 13.3516C16.9141 13.1172 16.6146 13 16.25 13ZM16.25 19.25H3.75V14.25H16.25V19.25ZM17.5 8V3.27344C17.5 2.90885 17.3828 2.60938 17.1484 2.375L15.625 0.851562C15.3646 0.617188 15.0651 0.5 14.7266 0.5H5C4.29688 0.526042 3.71094 0.773438 3.24219 1.24219C2.77344 1.71094 2.52604 2.29688 2.5 3V8C1.79688 8.02604 1.21094 8.27344 0.742188 8.74219C0.273438 9.21094 0.0260417 9.79688 0 10.5V14.875C0.0260417 15.2656 0.234375 15.474 0.625 15.5C1.01562 15.474 1.22396 15.2656 1.25 14.875V10.5C1.25 10.1354 1.36719 9.83594 1.60156 9.60156C1.83594 9.36719 2.13542 9.25 2.5 9.25H17.5C17.8646 9.25 18.1641 9.36719 18.3984 9.60156C18.6328 9.83594 18.75 10.1354 18.75 10.5V14.875C18.776 15.2656 18.9844 15.474 19.375 15.5C19.7656 15.474 19.974 15.2656 20 14.875V10.5C19.974 9.79688 19.7266 9.21094 19.2578 8.74219C18.7891 8.27344 18.2031 8.02604 17.5 8ZM16.25 8H3.75V3C3.75 2.63542 3.86719 2.33594 4.10156 2.10156C4.33594 1.86719 4.63542 1.75 5 1.75H14.7266L16.25 3.27344V8ZM16.875 10.1875C16.3021 10.2396 15.9896 10.5521 15.9375 11.125C15.9896 11.6979 16.3021 12.0104 16.875 12.0625C17.4479 12.0104 17.7604 11.6979 17.8125 11.125C17.7604 10.5521 17.4479 10.2396 16.875 10.1875Z" fill="#00C764" />
                            </svg>
                        </button>
                        <button id="download_btn" class="download_btn">
                            <svg width="25" height="19" viewBox="0 0 25 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.94531 11.1797C8.6849 10.8932 8.6849 10.6068 8.94531 10.3203C9.23177 10.0599 9.51823 10.0599 9.80469 10.3203L11.875 12.3516V6.375C11.901 5.98438 12.1094 5.77604 12.5 5.75C12.8906 5.77604 13.099 5.98438 13.125 6.375V12.3516L15.1953 10.3203C15.4818 10.0599 15.7682 10.0599 16.0547 10.3203C16.3151 10.6068 16.3151 10.8932 16.0547 11.1797L12.9297 14.3047C12.6432 14.5651 12.3568 14.5651 12.0703 14.3047L8.94531 11.1797ZM10.625 0.75C11.7969 0.75 12.8646 1.01042 13.8281 1.53125C14.8177 2.05208 15.625 2.76823 16.25 3.67969C16.8229 3.39323 17.4479 3.25 18.125 3.25C19.375 3.27604 20.4036 3.70573 21.2109 4.53906C22.0443 5.34635 22.474 6.375 22.5 7.625C22.5 8.01562 22.4479 8.41927 22.3438 8.83594C23.151 9.2526 23.7891 9.85156 24.2578 10.6328C24.7526 11.4141 25 12.2865 25 13.25C24.974 14.6562 24.4922 15.8411 23.5547 16.8047C22.5911 17.7422 21.4062 18.224 20 18.25H5.625C4.03646 18.1979 2.70833 17.651 1.64062 16.6094C0.598958 15.5417 0.0520833 14.2135 0 12.625C0.0260417 11.375 0.377604 10.2812 1.05469 9.34375C1.73177 8.40625 2.63021 7.72917 3.75 7.3125C3.88021 5.4375 4.58333 3.88802 5.85938 2.66406C7.13542 1.4401 8.72396 0.802083 10.625 0.75ZM10.625 2C9.08854 2.02604 7.78646 2.54688 6.71875 3.5625C5.67708 4.57812 5.10417 5.85417 5 7.39062C4.94792 7.91146 4.67448 8.27604 4.17969 8.48438C3.29427 8.79688 2.59115 9.33073 2.07031 10.0859C1.54948 10.8151 1.27604 11.6615 1.25 12.625C1.27604 13.875 1.70573 14.9036 2.53906 15.7109C3.34635 16.5443 4.375 16.974 5.625 17H20C21.0677 16.974 21.9531 16.6094 22.6562 15.9062C23.3594 15.2031 23.724 14.3177 23.75 13.25C23.75 12.5208 23.5677 11.8698 23.2031 11.2969C22.8385 10.724 22.3568 10.2682 21.7578 9.92969C21.2109 9.59115 21.0026 9.09635 21.1328 8.44531C21.2109 8.21094 21.25 7.9375 21.25 7.625C21.224 6.73958 20.9245 5.9974 20.3516 5.39844C19.7526 4.82552 19.0104 4.52604 18.125 4.5C17.6302 4.5 17.1875 4.60417 16.7969 4.8125C16.1719 5.04688 15.651 4.90365 15.2344 4.38281C14.7135 3.65365 14.0495 3.08073 13.2422 2.66406C12.4609 2.22135 11.5885 2 10.625 2Z" fill="#2D7CFE" />
                            </svg>
                        </button>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <!-- Invoice Conainter End -->

    <!--==============================
    All Js File
============================== -->
    <!-- Jquery -->
    <script src="{{ asset('assets_am/js/vendor/jquery-3.6.0.min.js') }}"></script>
    <!-- Bootstrap -->
    <script src="{{ asset('assets_am/js/bootstrap.min.js') }}"></script>
    <!-- PDF Generator -->
    <script src="{{ asset('assets_am/js/jspdf.min.js') }}"></script>
    <script src="{{ asset('assets_am/js/html2canvas.min.js') }}"></script>
    <!-- Main Js File -->
    <script src="{{ asset('assets_am/js/main.js') }}"></script>

</body>

</html>