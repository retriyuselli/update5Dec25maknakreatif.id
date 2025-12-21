@php
    // Get current user
    $user = auth()->user();
    $currentDate = now();

    // Get upcoming leave requests (approved and pending)
    $upcomingLeaves = $user
        ->leaveRequests()
        ->with('leaveType')
        ->whereIn('status', ['approved', 'pending'])
        ->where('start_date', '>=', $currentDate)
        ->orderBy('start_date', 'asc')
        ->take(5)
        ->get();

    // Get recent leave requests for context
    $recentLeaves = $user
        ->leaveRequests()
        ->with('leaveType')
        ->where('start_date', '<', $currentDate)
        ->orderBy('start_date', 'desc')
        ->take(3)
        ->get();

    // Calculate days until next leave (rounded to whole number)
    $nextLeave = $upcomingLeaves->first();
    $daysUntilNextLeave = $nextLeave ? (int) $currentDate->diffInDays($nextLeave->start_date, false) : null;
@endphp

<!-- Upcoming Events Section -->
<div class="bg-white rounded-xl shadow-lg border border-gray-100" style="font-family: 'Poppins', sans-serif;">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4 rounded-t-xl">
        <h3 class="text-xl font-bold text-black flex items-center">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            Acara Mendatang & Jadwal Cuti
        </h3>
        <p class="text-black text-sm mt-1">Aktivitas mendatang dan rencana cuti Anda</p>
    </div>

    <div class="p-6 space-y-6">
        <!-- Next Leave Countdown -->
        @if ($nextLeave)
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-semibold text-blue-800 mb-1">Cuti Terjadwal Berikutnya</h4>
                        <p class="text-lg font-bold text-blue-900">{{ $nextLeave->leaveType->name ?? 'Cuti' }}</p>
                        <p class="text-sm text-blue-700">
                            {{ $nextLeave->start_date->format('d F Y') }} - {{ $nextLeave->end_date->format('d F Y') }}
                        </p>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600">{{ $daysUntilNextLeave }}</div>
                        <div class="text-sm font-medium text-blue-700">
                            {{ $daysUntilNextLeave }} hari tersisa
                        </div>
                        @php
                            $statusClasses = match ($nextLeave->status) {
                                'approved' => 'bg-green-100 text-green-800 border-green-200',
                                'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                default => 'bg-gray-100 text-gray-800 border-gray-200',
                            };
                        @endphp
                        <span
                            class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-medium border {{ $statusClasses }}">
                            {{ ucfirst($nextLeave->status) }}
                        </span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Upcoming Leave Requests -->
        @if ($upcomingLeaves->isNotEmpty())
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h5 class="font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m2-6v6a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2m-2 6V9a2 2 0 012-2h2">
                        </path>
                    </svg>
                    Jadwal Cuti Mendatang
                </h5>
                <div class="space-y-3">
                    @foreach ($upcomingLeaves as $leave)
                        <div
                            class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-100 hover:shadow-sm transition-all duration-200">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span
                                        class="font-medium text-gray-800">{{ $leave->leaveType->name ?? 'N/A' }}</span>
                                    @php
                                        $statusClasses = match ($leave->status) {
                                            'approved' => 'bg-green-100 text-green-800 border-green-200',
                                            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                            'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                            default => 'bg-gray-100 text-gray-800 border-gray-200',
                                        };
                                        $daysFromNow = (int) $currentDate->diffInDays($leave->start_date, false);
                                    @endphp
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-medium border {{ $statusClasses }}">
                                        {{ ucfirst($leave->status) }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-sm text-gray-600">
                                        {{ $leave->start_date->format('d M') }} -
                                        {{ $leave->end_date->format('d M Y') }}
                                    </span>
                                    <div class="flex items-center space-x-3">
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ (int) $leave->total_days }} hari
                                        </span>
                                        <span class="text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded-full">
                                            dalam {{ $daysFromNow }} hari
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <!-- No Upcoming Events -->
            <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-xl p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                    </path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak Ada Acara Mendatang</h3>
                <p class="text-gray-600 mb-4">Anda tidak memiliki jadwal cuti atau acara yang akan datang.</p>
                <a href="/leave/show"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Rencanakan Cuti Anda
                </a>
            </div>
        @endif

        <!-- Recent Leave History (Optional) -->
        @if ($recentLeaves->isNotEmpty())
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h5 class="font-semibold text-gray-800 mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Riwayat Cuti Terbaru
                </h5>
                <div class="space-y-2">
                    @foreach ($recentLeaves as $leave)
                        <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-100">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span
                                        class="font-medium text-gray-800">{{ $leave->leaveType->name ?? 'N/A' }}</span>
                                    @php
                                        $statusClasses = match ($leave->status) {
                                            'approved' => 'bg-green-100 text-green-800 border-green-200',
                                            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                            'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                            default => 'bg-gray-100 text-gray-800 border-gray-200',
                                        };
                                    @endphp
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-medium border {{ $statusClasses }}">
                                        {{ ucfirst($leave->status) }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between mt-1">
                                    <span class="text-sm text-gray-600">
                                        {{ $leave->start_date->format('d M') }} -
                                        {{ $leave->end_date->format('d M Y') }}
                                    </span>
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ (int) $leave->total_days }} hari
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="space-y-3">
            <!-- Primary Action -->
            <a href="/leave/show"
                class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center group border-0 outline-none"
                style="background: linear-gradient(135deg, #9333ea 0%, #4f46e5 100%) !important;">
                <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform duration-200" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Ajukan Cuti Baru
            </a>

            <!-- Secondary Actions -->
            <div class="grid grid-cols-2 gap-3">
                <a href="/admin/leave-requests"
                    class="bg-blue-50 hover:bg-blue-100 text-blue-700 font-medium py-3 px-4 rounded-lg border border-blue-200 hover:border-blue-300 transition-all duration-200 flex items-center justify-center text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m2-6v6a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2m-2 6V9a2 2 0 012-2h2">
                        </path>
                    </svg>
                    Lihat Semua Permintaan
                </a>
                <a href="/admin/leave-requests?tableFilters[status][value]=pending"
                    class="bg-yellow-50 hover:bg-yellow-100 text-yellow-700 font-medium py-3 px-4 rounded-lg border border-yellow-200 hover:border-yellow-300 transition-all duration-200 flex items-center justify-center text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Menunggu Peninjauan
                </a>
            </div>
        </div>

        <!-- Tips Pengajuan Cuti -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                <div class="bg-blue-100 p-2 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                Tips Pengajuan Cuti
            </h3>
            <ul class="space-y-3 text-sm text-blue-700">
                <li class="flex items-start">
                    <svg class="w-4 h-4 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Ajukan permohonan minimal 3 hari sebelumnya
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Sertakan alasan yang jelas dan detail
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Periksa saldo cuti sebelum mengajukan
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Upload dokumen pendukung jika diperlukan
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Tentukan karyawan pengganti untuk kelancaran kerja
                </li>
                <li class="flex items-start">
                    <svg class="w-4 h-4 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Hubungi HR untuk situasi darurat
                </li>
            </ul>
        </div>
    </div>
</div>
