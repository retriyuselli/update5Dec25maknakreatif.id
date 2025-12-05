@php
    // Get current user with relationships
    $user = auth()->user();
    $latestPayroll = $user->payrolls()->latest()->first();
    $currentYear = date('Y');
    
    // Calculate leave statistics for current year
    $leaveStats = [
        'approved' => $user->leaveRequests()
            ->where('status', 'approved')
            ->whereYear('start_date', $currentYear)
            ->sum('total_days'),
        'pending' => $user->leaveRequests()
            ->where('status', 'pending')
            ->whereYear('start_date', $currentYear)
            ->sum('total_days'),
        'rejected' => $user->leaveRequests()
            ->where('status', 'rejected')
            ->whereYear('start_date', $currentYear)
            ->sum('total_days')
    ];
    
    // Leave breakdown by type for approved leaves only
    $leaveByType = $user->leaveRequests()
        ->with('leaveType')
        ->where('status', 'approved')
        ->whereYear('start_date', $currentYear)
        ->get()
        ->groupBy('leaveType.name')
        ->map(function($leaves) {
            return $leaves->sum('total_days');
        });
    
    // Annual leave calculations
    $annualLeaveAllowance = $user->annual_leave_quota ?? 12;
    
    // Ensure minimum 12 days and handle invalid small values
    if ($annualLeaveAllowance < 12) {
        $annualLeaveAllowance = 12;
    }
    
    $usedLeave = $leaveStats['approved'];
    $remainingLeave = max(0, $annualLeaveAllowance - $usedLeave);
    
    // Handle extreme cases where used leave exceeds allowance (data inconsistency)
    if ($usedLeave > $annualLeaveAllowance) {
        // For display purposes, cap the visual at 100% but show actual numbers
        $displayUsedLeave = $usedLeave;
        $remainingLeave = 0; // No remaining days if over quota
    } else {
        $displayUsedLeave = $usedLeave;
    }
    
    // Get recent leave requests (last 5)
    $recentLeaves = $user->leaveRequests()
        ->with('leaveType')
        ->latest()
        ->take(5)
        ->get();
@endphp

<!-- HR Salary & Leave Information Section -->
<div class="mt-4 bg-white rounded-xl shadow-lg overflow-hidden" style="font-family: 'Poppins', sans-serif;">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 00-2 2h-2a2 2 0 00-2 2v6a2 2 0 01-2 2H9z"></path>
            </svg>
                        Kompensasi Karyawan & Manajemen Cuti {{ $currentYear }}
        </h3>
    </div>
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100" style="font-family: 'Poppins', sans-serif;">
        <!-- Header Section -->
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- SALARY INFORMATION SECTION -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h4 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Informasi Gaji
                        </h4>
                        @if($latestPayroll)
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">
                                Aktif
                            </span>
                        @endif
                    </div>
                    
                    @if($latestPayroll)
                        <!-- Salary Cards -->
                        <div class="space-y-4">
                            <!-- Monthly Salary -->
                            <div class="bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 rounded-xl p-6 hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-emerald-700">Gaji Bulanan</p>
                                        <p class="text-2xl font-bold text-emerald-800">
                                            {{ 'Rp ' . (int) ($latestPayroll->monthly_salary ?? 0) }}
                                        </p>
                                    </div>
                                    <div class="p-3 bg-emerald-100 rounded-full">
                                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Annual Salary -->
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6 hover:shadow-md transition-all duration-300">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-blue-700">Gaji Tahunan</p>
                                        <p class="text-2xl font-bold text-blue-800">
                                            {{ 'Rp ' . (int) ($latestPayroll->calculated_annual_salary ?? 0) }}
                                        </p>
                                        <p class="text-xs text-blue-600 mt-1">Bulanan × 12 bulan</p>
                                    </div>
                                    <div class="p-3 bg-blue-100 rounded-full">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Bonus & Benefits -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-lg p-4 text-center">
                                    <p class="text-xs font-medium text-purple-700 mb-1">Bonus Tahunan</p>
                                    <p class="text-lg font-bold text-purple-800">
                                        {{ 'Rp ' . (int) ($latestPayroll->bonus ?? 0) }}
                                    </p>
                                </div>
                                <div class="bg-gradient-to-r from-orange-50 to-yellow-50 border border-orange-200 rounded-lg p-4 text-center">
                                    <p class="text-xs font-medium text-orange-700 mb-1">Total Kompensasi</p>
                                    <p class="text-lg font-bold text-orange-800">
                                        {{ 'Rp ' . (int) (($latestPayroll->monthly_salary ?? 0) * 12) }}
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Salary Info Footer -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Terakhir Diperbarui:</span>
                                    <span class="font-medium text-gray-800">
                                        {{ $latestPayroll->updated_at->format('d F Y') }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-sm mt-2">
                                    <span class="text-gray-600">Periode Gaji:</span>
                                    <span class="font-medium text-gray-800">
                                        {{ $latestPayroll->pay_period ?? 'Bulanan' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- No Salary Data -->
                        <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl p-8 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Data Gaji Tidak Tersedia</h3>
                            <p class="text-gray-600 mb-4">Informasi gaji Anda belum diatur.</p>
                            <p class="text-sm text-gray-500">Silakan hubungi Departemen HR untuk mengatur detail gaji Anda.</p>
                        </div>
                    @endif
                </div>

                <!-- LEAVE INFORMATION SECTION -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h4 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Manajemen Cuti {{ $currentYear }}
                        </h4>
                        {{-- <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">
                            {{ $remainingLeave }}/{{ $annualLeaveAllowance }} days left
                        </span> --}}
                    </div>

                    <!-- Leave Balance Overview -->
                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200 rounded-xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h5 class="font-semibold text-purple-800">Saldo Cuti Tahunan</h5>
                            <span class="text-sm font-medium text-purple-600">
                                {{ $usedLeave }} digunakan / {{ $annualLeaveAllowance }} total
                            </span>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="w-full bg-gray-300 rounded-full h-5 mb-3 shadow-inner overflow-hidden">
                            @php
                                // Calculate usage percentage with clear step-by-step logic
                                $actualUsagePercentage = $annualLeaveAllowance > 0 ? (($usedLeave / $annualLeaveAllowance) * 100) : 0;
                                $usagePercentage = round($actualUsagePercentage, 1);
                                
                                // For progress bar display, cap at 100% but show actual percentage in text
                                $displayUsagePercentage = min(100, $usagePercentage);
                                $remainingPercentage = max(0, 100 - $displayUsagePercentage);
                                
                                // Determine color based on actual usage percentage
                                if ($usagePercentage <= 50) {
                                    $progressColor = 'bg-emerald-500'; // Bright green - good status
                                    $shadowColor = 'shadow-emerald-300';
                                    $usedColor = 'bg-emerald-200';
                                } elseif ($usagePercentage <= 80) {
                                    $progressColor = 'bg-amber-500'; // Bright amber - warning
                                    $shadowColor = 'shadow-amber-300';
                                    $usedColor = 'bg-amber-200';
                                } elseif ($usagePercentage <= 100) {
                                    $progressColor = 'bg-red-500'; // Bright red - critical
                                    $shadowColor = 'shadow-red-300';
                                    $usedColor = 'bg-red-300';
                                } else {
                                    // Over quota - special handling
                                    $progressColor = 'bg-purple-500'; // Purple for over-quota
                                    $shadowColor = 'shadow-purple-300';
                                    $usedColor = 'bg-red-500';
                                }
                            @endphp
                            <!-- Single progress bar approach -->
                            <div class="w-full h-5 bg-gray-200 rounded-full relative overflow-hidden">
                                @if($usagePercentage > 100)
                                    {{-- Over quota - show full red bar with pattern --}}
                                    <div class="w-full h-5 bg-red-500 relative overflow-hidden">
                                        <div class="absolute inset-0 bg-gradient-to-r from-red-600 via-red-400 to-red-600 animate-pulse"></div>
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <span class="text-white text-xs font-bold">MELEBIHI KUOTA</span>
                                        </div>
                                    </div>
                                @else
                                    {{-- Normal quota - show used and remaining --}}
                                    <!-- Used portion -->
                                    @if($displayUsagePercentage > 0)
                                    <div class="absolute left-0 top-0 h-5 {{ $usedColor }} transition-all duration-700" 
                                         style="width: {{ $displayUsagePercentage }}%">
                                    </div>
                                    @endif
                                    
                                    <!-- Remaining portion -->
                                    @if($remainingPercentage > 0)
                                    <div class="absolute right-0 top-0 h-5 {{ $progressColor }} {{ $shadowColor }} 
                                         transition-all duration-700 shadow-lg relative overflow-hidden" 
                                         style="width: {{ $remainingPercentage }}%">
                                        <!-- Animated shimmer effect -->
                                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-pulse"></div>
                                        <!-- Inner glow effect -->
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/10 to-white/20"></div>
                                    </div>
                                    @endif
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center text-sm">
                            <div class="flex items-center">
                                @if($usagePercentage > 100)
                                    <div class="w-4 h-4 rounded-full border-2 border-white shadow-md bg-red-500 mr-3 relative">
                                        <div class="absolute inset-0 bg-red-600 rounded-full animate-ping opacity-75"></div>
                                    </div>
                                    <span class="font-bold text-red-600">MELEBIHI KUOTA sebanyak {{ $usedLeave - $annualLeaveAllowance }} hari</span>
                                @else
                                    <div class="w-4 h-4 rounded-full border-2 border-white shadow-md 
                                        {{ $displayUsagePercentage <= 50 ? 'bg-emerald-500' : ($displayUsagePercentage <= 80 ? 'bg-amber-500' : 'bg-red-500') }} 
                                        mr-3"></div>
                                    <span class="font-semibold text-gray-800">{{ $remainingLeave }} hari tersisa</span>
                                @endif
                            </div>
                            <span class="font-bold text-base {{ $usagePercentage > 100 ? 'text-red-600' : 'text-purple-700' }}">
                                {{ number_format($usagePercentage) }}% digunakan
                            </span>
                        </div>
                        
                        <!-- Additional info row -->
                        <div class="mt-4 pt-3 border-t border-purple-200 flex justify-between text-sm text-purple-700">
                            <span class="font-medium">Digunakan: {{ $usedLeave }}/{{ $annualLeaveAllowance }} hari</span>
                            @if($usagePercentage > 100)
                                <span class="text-red-600 font-bold bg-red-50 px-2 py-1 rounded">⚠️ Melebihi kuota</span>
                            @elseif($remainingLeave > 0)
                                <span class="font-bold text-emerald-600">✓ {{ $remainingLeave }} hari tersedia</span>
                            @else
                                <span class="text-red-600 font-bold bg-red-50 px-2 py-1 rounded">⚠️ Tidak ada hari tersisa</span>
                            @endif
                        </div>
                    </div>

                    <!-- Leave Statistics -->
                    <div class="grid grid-cols-3 gap-3">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center hover:shadow-sm transition-all duration-200">
                            <div class="text-2xl font-bold text-green-600 mb-1">{{ $leaveStats['approved'] }}</div>
                            <div class="text-xs font-medium text-green-700">Disetujui</div>
                        </div>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center hover:shadow-sm transition-all duration-200">
                            <div class="text-2xl font-bold text-yellow-600 mb-1">{{ $leaveStats['pending'] }}</div>
                            <div class="text-xs font-medium text-yellow-700">Menunggu</div>
                        </div>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center hover:shadow-sm transition-all duration-200">
                            <div class="text-2xl font-bold text-red-600 mb-1">{{ $leaveStats['rejected'] }}</div>
                            <div class="text-xs font-medium text-red-700">Ditolak</div>
                        </div>
                    </div>

                    <!-- Leave by Type -->
                    @if($leaveByType->isNotEmpty())
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h5 class="font-semibold text-gray-800 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m2-6v6a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2m-2 6V9a2 2 0 012-2h2"></path>
                                </svg>
                                Rincian Cuti berdasarkan Jenis
                            </h5>
                            <div class="space-y-2">
                                @foreach($leaveByType as $type => $days)
                                    <div class="flex items-center justify-between py-2 border-b border-gray-200 last:border-b-0">
                                        <span class="text-sm font-medium text-gray-700">{{ $type }}</span>
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">
                                            {{ $days }} hari
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
