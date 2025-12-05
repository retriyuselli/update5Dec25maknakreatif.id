<!-- HR Performance Dashboard Section -->
<div class="mt-4 bg-white rounded-xl shadow-lg overflow-hidden" style="font-family: 'Poppins', sans-serif;">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 00-2 2h-2a2 2 0 00-2 2v6a2 2 0 01-2 2H9z"></path>
            </svg>
            Performance Dashboard
        </h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Projects Completed -->
            <div class="text-center performance-metric p-4 rounded-lg transition-all duration-200 hover:scale-105" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);">
                <div class="w-16 h-16 bg-white/80 rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h4 class="text-3xl font-bold text-gray-900 mb-1" style="font-family: 'Poppins', sans-serif; font-weight: 700;">{{ $performanceData['projects_completed'] ?? '23' }}</h4>
                <p class="text-gray-700 text-sm font-medium" style="font-family: 'Poppins', sans-serif; font-weight: 500;">Projects Completed</p>
            </div>
            <!-- Client Satisfaction -->
            <div class="text-center performance-metric p-4 rounded-lg transition-all duration-200 hover:scale-105" style="background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);">
                <div class="w-16 h-16 bg-white/80 rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h4 class="text-3xl font-bold text-gray-900 mb-1" style="font-family: 'Poppins', sans-serif; font-weight: 700;">{{ $performanceData['client_satisfaction'] ?? '97' }}%</h4>
                <p class="text-gray-700 text-sm font-medium" style="font-family: 'Poppins', sans-serif; font-weight: 500;">Client Satisfaction</p>
            </div>
            <!-- Revenue Generated -->
            <div class="text-center performance-metric p-4 rounded-lg transition-all duration-200 hover:scale-105" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);">
                <div class="w-16 h-16 bg-white/80 rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <h4 class="text-3xl font-bold text-gray-900 mb-1" style="font-family: 'Poppins', sans-serif; font-weight: 700;">${{ number_format(($performanceData['revenue_generated'] ?? 125000) / 1000) }}K</h4>
                <p class="text-gray-700 text-sm font-medium" style="font-family: 'Poppins', sans-serif; font-weight: 500;">Revenue Generated</p>
            </div>
            <!-- Leave Balance -->
            <div class="text-center performance-metric p-4 rounded-lg transition-all duration-200 hover:scale-105" style="background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);">
                <div class="w-16 h-16 bg-white/80 rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h4 class="text-3xl font-bold text-gray-900 mb-1" style="font-family: 'Poppins', sans-serif; font-weight: 700;">{{ $hrData['remaining_leave'] ?? '18' }}</h4>
                <p class="text-gray-700 text-sm font-medium" style="font-family: 'Poppins', sans-serif; font-weight: 500;">Days Left</p>
            </div>
        </div>
    </div>
</div>
