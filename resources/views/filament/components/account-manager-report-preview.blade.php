<div class="p-5 font-poppins text-gray-600 max-h-[70vh] overflow-y-auto text-sm leading-relaxed">
    <!-- Header -->
    <div class="text-center mb-8 border-b-2 border-gray-300 pb-5">
        <div class="text-2xl font-bold text-blue-600 mb-1">PT. Makna Kreatif Indonesia</div>
        <div class="text-xl text-gray-900 mb-2">Laporan Kinerja Account Manager</div>
        <div class="text-sm text-gray-600">Periode: {{ $monthName }} {{ $year }}</div>
    </div>

    <!-- Account Manager Info -->
    <div style="margin-bottom: 25px;">
        <div style="font-size: 16px; font-weight: bold; color: #111111; margin-bottom: 15px; border-left: 4px solid #2D7CFE; padding-left: 10px;">
            Informasi Account Manager
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div style="background: #f3f3f3; padding: 15px; border-radius: 8px; border: 1px solid #E3E3E3;">
                <div style="font-weight: bold; color: #111111; margin-bottom: 5px;">Nama</div>
                <div style="font-size: 16px; color: #6E6E6E;">{{ $accountManager->name }}</div>
            </div>
            <div style="background: #f3f3f3; padding: 15px; border-radius: 8px; border: 1px solid #E3E3E3;">
                <div style="font-weight: bold; color: #111111; margin-bottom: 5px;">Email</div>
                <div style="font-size: 16px; color: #6E6E6E;">{{ $accountManager->email }}</div>
            </div>
            <div style="background: #f3f3f3; padding: 15px; border-radius: 8px; border: 1px solid #E3E3E3;">
                <div style="font-weight: bold; color: #111111; margin-bottom: 5px;">Periode Report</div>
                <div style="font-size: 16px; color: #6E6E6E;">{{ $monthName }} {{ $year }}</div>
            </div>
        </div>
    </div>

    <!-- Sales Performance -->
    <div style="margin-bottom: 25px;">
        <div style="font-size: 16px; font-weight: bold; color: #111111; margin-bottom: 15px; border-left: 4px solid #2D7CFE; padding-left: 10px;">
            Kinerja Penjualan
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px; margin-bottom: 15px;">
            <div style="background: linear-gradient(135deg, #2D7CFE 0%, #667eea 100%); color: white; padding: 15px; border-radius: 10px; text-align: center; box-shadow: 0 4px 15px rgba(45, 124, 254, 0.3);">
                <div style="font-size: 18px; font-weight: bold; margin-bottom: 5px;">{{ number_format($totalOrders) }}</div>
                <div style="font-size: 12px; opacity: 0.9;">Total Order</div>
            </div>
            <div style="background: linear-gradient(135deg, #2D7CFE 0%, #667eea 100%); color: white; padding: 15px; border-radius: 10px; text-align: center; box-shadow: 0 4px 15px rgba(45, 124, 254, 0.3);">
                <div style="font-size: 18px; font-weight: bold; margin-bottom: 5px;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                <div style="font-size: 12px; opacity: 0.9;">Total Revenue</div>
            </div>
            <div style="background: linear-gradient(135deg, #2D7CFE 0%, #667eea 100%); color: white; padding: 15px; border-radius: 10px; text-align: center; box-shadow: 0 4px 15px rgba(45, 124, 254, 0.3);">
                <div style="font-size: 18px; font-weight: bold; margin-bottom: 5px;">Rp {{ number_format($averageOrderValue, 0, ',', '.') }}</div>
                <div style="font-size: 12px; opacity: 0.9;">Rata-rata Order</div>
            </div>
            @if($target)
            <div style="background: linear-gradient(135deg, #2D7CFE 0%, #667eea 100%); color: white; padding: 15px; border-radius: 10px; text-align: center; box-shadow: 0 4px 15px rgba(45, 124, 254, 0.3);">
                <div style="font-size: 18px; font-weight: bold; margin-bottom: 5px;">{{ number_format($achievementPercentage, 1) }}%</div>
                <div style="font-size: 12px; opacity: 0.9;">Pencapaian Target</div>
            </div>
            @endif
        </div>

        @if($target)
        <div style="background: #f3f3f3; padding: 15px; border-radius: 8px; border: 1px solid #E3E3E3;">
            <div style="font-weight: bold; color: #111111; margin-bottom: 10px;">Target vs Achievement</div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 14px;">
                <span>Target: <span style="color: #2D7CFE; font-weight: bold;">Rp {{ number_format($target->target_amount, 0, ',', '.') }}</span></span>
                <span>Achievement: <span style="color: #2D7CFE; font-weight: bold;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span></span>
            </div>
            <div style="background: #E3E3E3; border-radius: 10px; height: 15px; overflow: hidden;">
                <div style="height: 100%; background: linear-gradient(90deg, #2D7CFE, #667eea); width: {{ min($achievementPercentage, 100) }}%;"></div>
            </div>
        </div>
        @endif
    </div>

    <!-- Orders Detail -->
    @if($orders && $orders->count() > 0)
    <div style="margin-bottom: 25px;">
        <div style="font-size: 16px; font-weight: bold; color: #111111; margin-bottom: 15px; border-left: 4px solid #2D7CFE; padding-left: 10px;">
            Detail Order ({{ $orders->count() }} order)
        </div>
        <div style="max-height: 300px; overflow-y: auto;">
            <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(45, 124, 254, 0.1);">
                <thead>
                    <tr style="background: #E1ECFF;">
                        <th style="padding: 10px; text-align: left; font-weight: bold; color: #111111; border-bottom: 1px solid #E3E3E3; text-transform: uppercase;">No</th>
                        <th style="padding: 10px; text-align: left; font-weight: bold; color: #111111; border-bottom: 1px solid #E3E3E3; text-transform: uppercase;">Prospect</th>
                        <th style="padding: 10px; text-align: left; font-weight: bold; color: #111111; border-bottom: 1px solid #E3E3E3; text-transform: uppercase;">Closing Date</th>
                        <th style="padding: 10px; text-align: left; font-weight: bold; color: #111111; border-bottom: 1px solid #E3E3E3; text-transform: uppercase;">Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $index => $order)
                    <tr style="border-bottom: 1px solid #E3E3E3;">
                        <td style="padding: 10px; color: #6E6E6E;">{{ $index + 1 }}</td>
                        <td style="padding: 10px; color: #111111; font-weight: 500;">{{ $order->prospect->name_event ?? 'N/A' }}</td>
                        <td style="padding: 10px; color: #72849B;">{{ $order->closing_date ? $order->closing_date->format('d F Y') : '-' }}</td>
                        <td style="padding: 10px; color: #2D7CFE; font-weight: bold;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Payroll Data -->
    @if($payrollData)
    <div style="margin-bottom: 25px;">
        <div style="font-size: 16px; font-weight: bold; color: #111111; margin-bottom: 15px; border-left: 4px solid #2D7CFE; padding-left: 10px;">
            Data Payroll
        </div>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div style="background: #f3f3f3; padding: 15px; border-radius: 8px; border: 1px solid #E3E3E3;">
                <div style="font-weight: bold; color: #111111; margin-bottom: 5px;">Gaji Pokok</div>
                <div style="font-size: 16px; color: #2D7CFE; font-weight: bold;">Rp {{ number_format($payrollData->gaji_pokok ?? 0, 0, ',', '.') }}</div>
            </div>
            <div style="background: #f3f3f3; padding: 15px; border-radius: 8px; border: 1px solid #E3E3E3;">
                <div style="font-weight: bold; color: #111111; margin-bottom: 5px;">Tunjangan</div>
                <div style="font-size: 16px; color: #2D7CFE; font-weight: bold;">Rp {{ number_format($payrollData->tunjangan ?? 0, 0, ',', '.') }}</div>
            </div>
            <div style="background: #f3f3f3; padding: 15px; border-radius: 8px; border: 1px solid #E3E3E3;">
                <div style="font-weight: bold; color: #111111; margin-bottom: 5px;">Bonus</div>
                <div style="font-size: 16px; color: #2D7CFE; font-weight: bold;">Rp {{ number_format($payrollData->bonus ?? 0, 0, ',', '.') }}</div>
            </div>
            <div style="background: #f3f3f3; padding: 15px; border-radius: 8px; border: 1px solid #E3E3E3;">
                <div style="font-weight: bold; color: #111111; margin-bottom: 5px;">Gaji Bulanan</div>
                <div style="font-size: 16px; color: #2D7CFE; font-weight: bold;">Rp {{ number_format($payrollData->monthly_salary ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Leave Data -->
    @if($leaveData && $leaveData->count() > 0)
    <div style="margin-bottom: 25px;">
        <div style="font-size: 16px; font-weight: bold; color: #111111; margin-bottom: 15px; border-left: 4px solid #2D7CFE; padding-left: 10px;">
            Riwayat Cuti ({{ $leaveData->count() }} cuti)
        </div>
        <div style="max-height: 200px; overflow-y: auto;">
            <table style="width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(45, 124, 254, 0.1);">
                <thead>
                    <tr style="background: #E1ECFF;">
                        <th style="padding: 10px; text-align: left; font-weight: bold; color: #111111; border-bottom: 1px solid #E3E3E3; text-transform: uppercase;">Tanggal</th>
                        <th style="padding: 10px; text-align: left; font-weight: bold; color: #111111; border-bottom: 1px solid #E3E3E3; text-transform: uppercase;">Jenis</th>
                        <th style="padding: 10px; text-align: left; font-weight: bold; color: #111111; border-bottom: 1px solid #E3E3E3; text-transform: uppercase;">Durasi</th>
                        <th style="padding: 10px; text-align: left; font-weight: bold; color: #111111; border-bottom: 1px solid #E3E3E3; text-transform: uppercase;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaveData as $leave)
                    <tr style="border-bottom: 1px solid #E3E3E3;">
                        <td style="padding: 10px; color: #6E6E6E;">{{ $leave->start_date ? $leave->start_date->format('d M Y') : '-' }}</td>
                        <td style="padding: 10px; color: #111111;">{{ $leave->leaveType->name ?? 'N/A' }}</td>
                        <td style="padding: 10px; color: #72849B;">{{ $leave->total_days ?? 0 }} hari</td>
                        <td style="padding: 10px;">
                            <span style="padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 500; {{ $leave->status === 'approved' ? 'background: #E1ECFF; color: #2D7CFE;' : ($leave->status === 'pending' ? 'background: #fef3c7; color: #92400e;' : 'background: #fee2e2; color: #991b1b;') }}">
                                {{ ucfirst($leave->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Summary -->
    <div style="margin-bottom: 20px;">
        <div style="font-size: 16px; font-weight: bold; color: #111111; margin-bottom: 15px; border-left: 4px solid #2D7CFE; padding-left: 10px;">
            Ringkasan Kinerja
        </div>
        <div style="background: #f3f3f3; padding: 15px; border-radius: 8px; border: 1px solid #E3E3E3;">
            <h4 style="margin: 0 0 10px 0; color: #111111; font-size: 16px;">Evaluasi Periode {{ $monthName }} {{ $year }}</h4>
            <ul style="margin: 0 0 15px 0; padding-left: 20px; color: #6E6E6E; font-size: 14px;">
                <li>Berhasil menutup {{ $totalOrders }} order dengan total revenue Rp {{ number_format($totalRevenue, 0, ',', '.') }}</li>
                @if($target)
                <li>Pencapaian target: {{ number_format($achievementPercentage, 1) }}% dari target Rp {{ number_format($target->target_amount, 0, ',', '.') }}</li>
                @endif
                <li>Rata-rata nilai per order: Rp {{ number_format($averageOrderValue, 0, ',', '.') }}</li>
                @if($leaveData && $leaveData->count() > 0)
                <li>Total cuti yang diambil: {{ $leaveData->count() }} kali</li>
                @endif
            </ul>
            
            <!-- Motivational Message -->
            <div style="background: linear-gradient(135deg, #2D7CFE 0%, #667eea 100%); color: white; padding: 15px; border-radius: 10px; margin-top: 15px; box-shadow: 0 4px 15px rgba(45, 124, 254, 0.3);">
                <div style="font-size: 16px; font-weight: bold; margin-bottom: 8px; text-align: center;">💪 Pesan Motivasi</div>
                <div style="font-size: 14px; text-align: center; line-height: 1.5;">
                    @if($target && $achievementPercentage >= 100)
                        🎉 <strong>Luar Biasa!</strong> Anda telah melampaui target! Dedikasi dan kerja keras Anda membuahkan hasil yang gemilang. Terus pertahankan momentum ini dan raih pencapaian yang lebih tinggi!
                    @elseif($target && $achievementPercentage >= 80)
                        🚀 <strong>Hampir Sempurna!</strong> Anda sudah sangat dekat dengan target! Sedikit lagi untuk mencapai kesuksesan. Tetap fokus dan berikan yang terbaik di periode berikutnya!
                    @elseif($target && $achievementPercentage >= 60)
                        💯 <strong>Keep Going!</strong> Progres yang baik! Setiap langkah membawa Anda lebih dekat ke target. Tingkatkan strategi dan raih lebih banyak peluang di bulan depan!
                    @elseif($target && $achievementPercentage >= 40)
                        🎯 <strong>Saatnya Berakselerasi!</strong> Masih banyak peluang menanti! Evaluasi strategi, perkuat networking, dan jadikan setiap prospek sebagai kesempatan emas untuk meraih target!
                    @else
                        🔥 <strong>Bangkit dan Berjuang!</strong> Setiap tantangan adalah kesempatan untuk tumbuh! Jadikan periode ini sebagai motivasi untuk memberikan performa terbaik. Success is coming!
                    @endif
                </div>
                <div style="text-align: center; margin-top: 10px; font-size: 13px; font-style: italic; opacity: 0.9;">
                    "Kesuksesan bukan tentang tidak pernah jatuh, tapi tentang bangkit setiap kali terjatuh"
                </div>
            </div>
        </div>
    </div>

    <div style="text-align: center; padding-top: 15px; border-top: 1px solid #E3E3E3; color: #72849B; font-size: 12px;">
        <p style="margin: 0;">Report ini di-generate secara otomatis dari sistem Account Manager Target</p>
        <p style="margin: 5px 0 0 0;">PT. Makna Kreatif - {{ date('Y') }}</p>
    </div>
</div>