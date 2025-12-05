@extends('layouts.app')

@section('title', 'Status Permohonan Cuti')

@section('content')
    @include('front.header')

    <div class="min-h-screen bg-gray-50 py-12" style="font-family: 'Poppins', sans-serif;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-600 rounded-full mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m2-6v6a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2m-2 6V9a2 2 0 012-2h2" />
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Status Permohonan Cuti</h1>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Pantau status dan riwayat permohonan cuti Anda</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-3">
                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <!-- Total Requests -->
                        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                            <div class="flex items-center">
                                <div class="bg-blue-100 p-3 rounded-lg">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m2-6v6a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2m-2 6V9a2 2 0 012-2h2" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-medium text-gray-500">Total Permohonan</h3>
                                    <p class="text-2xl font-bold text-gray-900">{{ $totalRequests ?? 0 }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Approved -->
                        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                            <div class="flex items-center">
                                <div class="bg-green-100 p-3 rounded-lg">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-medium text-gray-500">Disetujui</h3>
                                    <p class="text-2xl font-bold text-green-600">{{ $approvedRequests ?? 0 }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Pending -->
                        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                            <div class="flex items-center">
                                <div class="bg-yellow-100 p-3 rounded-lg">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-sm font-medium text-gray-500">Menunggu</h3>
                                    <p class="text-2xl font-bold text-yellow-600">{{ $pendingRequests ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Leave Requests List -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                        <!-- Header -->
                        <div class="bg-blue-600 px-6 py-4">
                            <h2 class="text-xl font-bold text-white">Riwayat Permohonan Cuti</h2>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            @if(isset($leaveRequests) && $leaveRequests->count() > 0)
                                <div class="space-y-4">
                                    @foreach($leaveRequests as $request)
                                        <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow duration-200">
                                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                                                <!-- Left Content -->
                                                <div class="flex-1">
                                                    <div class="flex items-center mb-2">
                                                        <h3 class="text-lg font-semibold text-gray-900 mr-3">
                                                            {{ $request->leaveType->name ?? 'Jenis Cuti' }}
                                                        </h3>
                                                        @if($request->status == 'approved')
                                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                                </svg>
                                                                Disetujui
                                                            </span>
                                                        @elseif($request->status == 'pending')
                                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                                </svg>
                                                                Menunggu
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                                </svg>
                                                                Ditolak
                                                            </span>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600 mb-3">
                                                        <div class="flex items-center">
                                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                            <span>{{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}</span>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            <span>{{ $request->total_days }} hari</span>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                            <span>{{ \Carbon\Carbon::parse($request->created_at)->format('d M Y') }}</span>
                                                        </div>
                                                    </div>

                                                    @if($request->reason)
                                                        <p class="text-gray-700 mb-3">
                                                            <span class="font-medium">Alasan:</span> {{ Str::limit($request->reason, 100) }}
                                                        </p>
                                                    @endif

                                                    @if($request->replacementEmployee)
                                                        <div class="flex items-center text-sm text-blue-600 mb-3">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                            </svg>
                                                            <span><strong>Pengganti:</strong> {{ $request->replacementEmployee->name }}</span>
                                                        </div>
                                                    @endif

                                                    @if($request->approver && $request->status != 'pending')
                                                        <div class="text-sm text-gray-500">
                                                            <span class="font-medium">
                                                                {{ $request->status == 'approved' ? 'Disetujui' : 'Ditolak' }} oleh:
                                                            </span> 
                                                            {{ $request->approver->name }}
                                                        </div>
                                                    @endif

                                                    @if($request->approval_notes && $request->status != 'pending')
                                                        <div class="mt-2 p-3 bg-gray-50 rounded-lg">
                                                            <p class="text-sm text-gray-700">
                                                                <span class="font-medium">Catatan:</span> {{ $request->approval_notes }}
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Right Actions -->
                                                <div class="mt-4 lg:mt-0 lg:ml-6 flex flex-col space-y-2">
                                                    @if($request->status == 'pending')
                                                        <button onclick="editRequest({{ $request->id }})" 
                                                            class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors duration-200 flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                            Edit
                                                        </button>
                                                        <button onclick="cancelRequest({{ $request->id }})" 
                                                            class="px-4 py-2 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors duration-200 flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                            Batalkan
                                                        </button>
                                                    @endif
                                                    <button onclick="viewDetails({{ $request->id }})" 
                                                        class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors duration-200 flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        Lihat Detail
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Pagination -->
                                @if($leaveRequests->hasPages())
                                    <div class="mt-8 flex justify-center">
                                        {{ $leaveRequests->links() }}
                                    </div>
                                @endif
                            @else
                                <!-- Empty State -->
                                <div class="text-center py-12">
                                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m2-6v6a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2m-2 6V9a2 2 0 012-2h2" />
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum Ada Permohonan Cuti</h3>
                                    <p class="text-gray-500 mb-4">Anda belum mengajukan permohonan cuti.</p>
                                    <a href="{{ route('leave.create') }}" 
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Ajukan Cuti Baru
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi Cepat</h3>
                        <div class="space-y-3">
                            <a href="{{ route('leave.create') }}"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Ajukan Cuti Baru
                            </a>
                            <a href="/admin/leave-requests"
                                class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Panel Admin
                            </a>
                        </div>
                    </div>

                    <!-- Leave Summary -->
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <div class="bg-green-100 p-2 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            Ringkasan Tahun Ini
                        </h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Total Jatah:</span>
                                <span class="font-semibold text-gray-800">{{ $annualLeaveAllowance ?? 12 }} hari</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Telah Digunakan:</span>
                                <span class="font-semibold text-red-600">{{ $usedLeave ?? 0 }} hari</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Sisa Tersedia:</span>
                                <span class="font-semibold text-green-600">{{ $remainingLeave ?? 12 }} hari</span>
                            </div>
                        </div>
                    </div>

                    <!-- Help & Support -->
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                            <div class="bg-blue-100 p-2 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            Bantuan
                        </h3>
                        <p class="text-sm text-blue-700 mb-4">
                            Butuh bantuan dengan permohonan cuti Anda? Hubungi tim HR kami.
                        </p>
                        <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Kontak HR →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Detail -->
    <div id="detailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900">Detail Permohonan Cuti</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div id="modalContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Store request data for modal
        const requestsData = {
            @foreach($leaveRequests as $request)
            {{ $request->id }}: {
                id: {{ $request->id }},
                leaveType: "{{ $request->leaveType?->name ?? 'Jenis Cuti' }}",
                startDate: "{{ \Carbon\Carbon::parse($request->start_date)->format('d M Y') }}",
                endDate: "{{ \Carbon\Carbon::parse($request->end_date)->format('d M Y') }}",
                totalDays: {{ $request->total_days }},
                reason: {!! json_encode($request->reason ?? '') !!},
                emergencyContact: {!! json_encode($request->emergency_contact ?? '') !!},
                replacementEmployee: "{{ $request->replacementEmployee?->name ?? '' }}",
                replacementDepartment: "{{ $request->replacementEmployee?->department ?? '' }}",
                status: "{{ $request->status }}",
                approver: "{{ $request->approver?->name ?? '' }}",
                approvalNotes: {!! json_encode($request->approval_notes ?? '') !!},
                createdAt: "{{ \Carbon\Carbon::parse($request->created_at)->format('d M Y H:i') }}",
                documents: {!! json_encode(is_string($request->documents) ? json_decode($request->documents, true) ?? [] : ($request->documents ?? [])) !!}
            }{{ $loop->last ? '' : ',' }}
            @endforeach
        };

        function viewDetails(requestId) {
            const modal = document.getElementById('detailModal');
            const content = document.getElementById('modalContent');
            
            console.log('Opening modal for request ID:', requestId);
            console.log('Available request data:', requestsData);
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            const request = requestsData[requestId];
            if (!request) {
                console.error('Request not found:', requestId);
                console.log('Available request IDs:', Object.keys(requestsData));
                content.innerHTML = '<div class="text-center py-8"><p class="text-red-600">Data tidak ditemukan untuk ID: ' + requestId + '</p><p class="text-sm text-gray-500 mt-2">ID yang tersedia: ' + Object.keys(requestsData).join(', ') + '</p></div>';
                return;
            }
            
            console.log('Request data found:', request);
            console.log('Request status:', request.status);

            try {
                const statusBadge = getStatusBadge(request.status);
                
                // Ensure documents is an array
                let documents = request.documents || [];
                if (typeof documents === 'string') {
                    try {
                        documents = JSON.parse(documents);
                    } catch (e) {
                        documents = [];
                    }
                }
                if (!Array.isArray(documents)) {
                    documents = [];
                }
                
                const documentsHtml = documents.length > 0 
                    ? documents.map(doc => {
                        const fileName = doc.split('/').pop(); // Get filename from path
                        const fileExtension = fileName.split('.').pop().toLowerCase();
                        const fileIcon = getFileIcon(fileExtension);
                        return `
                            <div class="flex items-center justify-between bg-gray-50 border border-gray-200 rounded-lg p-3 mb-2">
                                <div class="flex items-center">
                                    ${fileIcon}
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">${fileName}</p>
                                        <p class="text-xs text-gray-500">${fileExtension.toUpperCase()}</p>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="viewDocument('${doc}')" 
                                        class="inline-flex items-center px-3 py-1 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded hover:bg-blue-100 transition-colors duration-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Lihat
                                    </button>
                                    <button onclick="downloadDocument('${doc}')" 
                                        class="inline-flex items-center px-3 py-1 text-xs font-medium text-gray-600 bg-gray-50 border border-gray-200 rounded hover:bg-gray-100 transition-colors duration-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Download
                                    </button>
                                </div>
                            </div>
                        `;
                    }).join('')
                    : '<span class="text-gray-500 text-sm">Tidak ada dokumen</span>';

                content.innerHTML = `
                    <div class="space-y-6">
                        <!-- Header -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-xl font-bold text-gray-900 mb-2">${request.leaveType}</h4>
                                    <p class="text-gray-600">ID Permohonan: #${request.id}</p>
                                </div>
                                <div class="text-right">
                                    ${statusBadge}
                                    <p class="text-sm text-gray-500 mt-2">Diajukan: ${request.createdAt}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Periode Cuti -->
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <h5 class="font-semibold text-gray-900 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Periode Cuti
                                </h5>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Tanggal Mulai:</span>
                                        <span class="font-medium">${request.startDate}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Tanggal Selesai:</span>
                                        <span class="font-medium">${request.endDate}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Total Hari:</span>
                                        <span class="font-bold text-blue-600">${request.totalDays} hari</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Kontak & Pengganti -->
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <h5 class="font-semibold text-gray-900 mb-3 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    Informasi Kontak
                                </h5>
                                <div class="space-y-2">
                                    <div>
                                        <span class="text-gray-600 block">Kontak Darurat:</span>
                                        <span class="font-medium">${request.emergencyContact || 'Tidak diisi'}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600 block">Karyawan Pengganti:</span>
                                        <span class="font-medium">${request.replacementEmployee || 'Tidak ada'}</span>
                                        ${request.replacementDepartment ? `<span class="text-sm text-gray-500 block">${request.replacementDepartment}</span>` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Alasan Cuti -->
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <h5 class="font-semibold text-gray-900 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Alasan Cuti
                            </h5>
                            <p class="text-gray-700 leading-relaxed">${request.reason || 'Tidak ada alasan yang diberikan'}</p>
                        </div>

                        <!-- Dokumen Pendukung -->
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <h5 class="font-semibold text-gray-900 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Dokumen Pendukung
                            </h5>
                            <div>${documentsHtml}</div>
                        </div>

                        ${request.status !== 'pending' ? `
                        <!-- Status Persetujuan -->
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <h5 class="font-semibold text-gray-900 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Status Persetujuan
                            </h5>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">${request.status === 'approved' ? 'Disetujui' : 'Ditolak'} oleh:</span>
                                    <span class="font-medium">${request.approver || 'Tidak diketahui'}</span>
                                </div>
                                ${request.approvalNotes ? `
                                <div>
                                    <span class="text-gray-600 block">Catatan:</span>
                                    <p class="text-gray-700 bg-gray-50 p-3 rounded mt-1">${request.approvalNotes}</p>
                                </div>
                                ` : ''}
                            </div>
                        </div>
                        ` : ''}

                        <!-- Actions -->
                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                            ${request.status === 'pending' ? `
                            <button onclick="editRequest(${request.id})" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Permohonan
                            </button>
                            ` : ''}
                            <button onclick="closeModal()" 
                                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                                Tutup
                            </button>
                        </div>
                    </div>
                `;
            } catch (error) {
                console.error('Error generating modal content:', error);
                content.innerHTML = '<div class="text-center py-8"><p class="text-red-600">Terjadi kesalahan saat memuat detail: ' + error.message + '</p></div>';
            }
        }

        function getStatusBadge(status) {
            switch(status) {
                case 'approved':
                    return '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>Disetujui</span>';
                case 'pending':
                    return '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" /></svg>Menunggu</span>';
                default:
                    return '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800"><svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>Ditolak</span>';
            }
        }

        function closeModal() {
            const modal = document.getElementById('detailModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function editRequest(requestId) {
            // Redirect to edit page
            window.location.href = `/leave/show?edit=${requestId}`;
        }

        function cancelRequest(requestId) {
            if (confirm('Apakah Anda yakin ingin membatalkan dan menghapus permohonan cuti ini?\n\nPermohonan yang dihapus tidak dapat dikembalikan.')) {
                // Show loading state
                const cancelButton = document.querySelector(`button[onclick="cancelRequest(${requestId})"]`);
                const originalText = cancelButton.innerHTML;
                cancelButton.innerHTML = '<svg class="w-4 h-4 mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Menghapus...';
                cancelButton.disabled = true;
                
                // CSRF token
                const token = document.querySelector('meta[name="csrf-token"]');
                
                fetch(`/leave/${requestId}/cancel`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token ? token.getAttribute('content') : '',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        alert(data.message);
                        // Reload page to reflect changes
                        location.reload();
                    } else {
                        // Show error message
                        alert(data.message || 'Terjadi kesalahan saat membatalkan permohonan.');
                        // Restore button
                        cancelButton.innerHTML = originalText;
                        cancelButton.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan jaringan. Silakan coba lagi.');
                    // Restore button
                    cancelButton.innerHTML = originalText;
                    cancelButton.disabled = false;
                });
            }
        }

        // Function to get file icon based on extension
        function getFileIcon(extension) {
            switch(extension) {
                case 'pdf':
                    return '<svg class="w-8 h-8 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" /></svg>';
                case 'jpg':
                case 'jpeg':
                case 'png':
                    return '<svg class="w-8 h-8 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" /></svg>';
                case 'doc':
                case 'docx':
                    return '<svg class="w-8 h-8 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" /></svg>';
                default:
                    return '<svg class="w-8 h-8 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" /></svg>';
            }
        }

        // Function to view document
        function viewDocument(documentPath) {
            const fullUrl = `/storage/${documentPath}`;
            
            // Check if it's an image
            const extension = documentPath.split('.').pop().toLowerCase();
            const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
            
            if (imageExtensions.includes(extension)) {
                // Open image in a new modal
                showImageModal(fullUrl, documentPath.split('/').pop());
            } else {
                // Open document in new tab
                window.open(fullUrl, '_blank');
            }
        }

        // Function to download document
        function downloadDocument(documentPath) {
            console.log('Download function called with:', documentPath);
            
            // Create download URL that goes through Laravel route for security
            const downloadUrl = `/leave/document/download/${encodeURIComponent(documentPath)}`;
            console.log('Download URL:', downloadUrl);
            
            // Use window.location for direct download (will trigger browser download)
            window.location.href = downloadUrl;
        }

        // Function to show image in modal
        function showImageModal(imageUrl, fileName) {
            // Create modal HTML
            const modalHtml = `
                <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
                    <div class="max-w-4xl max-h-full mx-4 relative">
                        <div class="bg-white rounded-lg shadow-xl overflow-hidden">
                            <div class="flex justify-between items-center p-4 border-b">
                                <h3 class="text-lg font-semibold text-gray-900">${fileName}</h3>
                                <button onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div class="p-4">
                                <img src="${imageUrl}" alt="${fileName}" class="max-w-full max-h-96 mx-auto rounded">
                            </div>
                            <div class="flex justify-end p-4 border-t bg-gray-50">
                                <button onclick="downloadDocument('${imageUrl.replace('/storage/', '')}')" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Download
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Add modal to body
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            
            // Add event listener for clicking outside
            document.getElementById('imageModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeImageModal();
                }
            });
        }

        // Function to close image modal
        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            if (modal) {
                modal.remove();
            }
        }

        // Close modal when clicking outside
        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
                closeImageModal();
            }
        });
    </script>
@endsection
