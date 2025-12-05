<!-- HR Benefits Section -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden" style="font-family: 'Poppins', sans-serif;">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
            Employee Benefits
        </h3>
    </div>
    <div class="p-6">
        <div class="space-y-3">
            @forelse($benefits ?? [
                'health_insurance' => 'Full Coverage',
                'annual_leave' => '24 Days',
                'performance_bonus' => 'Up to 2.5M',
                'training_budget' => '5M per year',
                'flexible_hours' => 'Available',
                'remote_work' => '2 days/week'
            ] as $key => $value)
            <div class="flex items-center justify-between p-3 rounded-lg transition-all duration-200 hover:scale-105" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
                <div class="flex items-center">
                    @if($key == 'health_insurance')
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                    @elseif($key == 'annual_leave')
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @elseif($key == 'performance_bonus')
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    @elseif($key == 'training_budget')
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    @elseif($key == 'flexible_hours')
                        <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    @elseif($key == 'remote_work')
                        <div class="w-8 h-8 bg-teal-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v0"></path>
                            </svg>
                        </div>
                    @else
                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    @endif
                    <span class="text-sm text-gray-700 font-medium" style="font-family: 'Poppins', sans-serif; font-weight: 500;">{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                </div>
                <span class="text-sm font-medium px-3 py-1 rounded-full
                    @if($key == 'health_insurance') bg-green-100 text-green-700
                    @elseif($key == 'annual_leave') bg-blue-100 text-blue-700
                    @elseif($key == 'performance_bonus') bg-yellow-100 text-yellow-700
                    @elseif($key == 'training_budget') bg-purple-100 text-purple-700
                    @elseif($key == 'flexible_hours') bg-indigo-100 text-indigo-700
                    @elseif($key == 'remote_work') bg-teal-100 text-teal-700
                    @else bg-gray-100 text-gray-700
                    @endif" style="font-family: 'Poppins', sans-serif; font-weight: 500;">{{ $value }}</span>
            </div>
            @empty
            <div class="text-center text-gray-500 py-8">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                <p class="text-sm" style="font-family: 'Poppins', sans-serif; font-weight: 400;">No benefits information available</p>
            </div>
            @endforelse
        </div>
        
        <!-- Quick Salary Info -->
        @if(isset($hrData))
        <div class="mt-6 pt-4 border-t border-gray-200">
            <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-4 transition-all duration-200 hover:scale-105">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-200 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700" style="font-family: 'Poppins', sans-serif; font-weight: 500;">Monthly Salary</span>
                    </div>
                    <span class="text-lg font-bold text-green-700" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                        Rp {{ number_format($hrData['monthly_salary'] ?? 8500000, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
