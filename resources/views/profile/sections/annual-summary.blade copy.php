<!-- Annual Summary Section -->
<div class="mt-4 bg-white rounded-xl shadow-lg overflow-hidden" style="font-family: 'Poppins', sans-serif;">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m2-6v6a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2m-2 6V9a2 2 0 012-2h2"></path>
            </svg>
            Annual Summary {{ $hrData['annual_summary']['year'] ?? '2024' }}
        </h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Days Worked -->
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-4 transition-all duration-200 hover:scale-105">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-sm font-medium text-gray-600" style="font-family: 'Poppins', sans-serif; font-weight: 500;">Days Worked</h4>
                        <p class="text-2xl font-bold text-gray-900" style="font-family: 'Poppins', sans-serif; font-weight: 700;">{{ $hrData['annual_summary']['total_days_worked'] ?? '240' }}</p>
                        <p class="text-xs text-gray-500" style="font-family: 'Poppins', sans-serif; font-weight: 400;">Out of 365 days</p>
                    </div>
                </div>
            </div>

            <!-- Leave Taken -->
            <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg p-4 transition-all duration-200 hover:scale-105">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-7 4h12l-1 5H9l-1-5z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-sm font-medium text-gray-600" style="font-family: 'Poppins', sans-serif; font-weight: 500;">Leave Taken</h4>
                        <p class="text-2xl font-bold text-gray-900" style="font-family: 'Poppins', sans-serif; font-weight: 700;">{{ $hrData['annual_summary']['total_leave_taken'] ?? '6' }}</p>
                        <p class="text-xs text-gray-500" style="font-family: 'Poppins', sans-serif; font-weight: 400;">Days this year</p>
                    </div>
                </div>
            </div>

            <!-- Overtime Hours -->
            <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-lg p-4 transition-all duration-200 hover:scale-105">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-sm font-medium text-gray-600" style="font-family: 'Poppins', sans-serif; font-weight: 500;">Overtime Hours</h4>
                        <p class="text-2xl font-bold text-gray-900" style="font-family: 'Poppins', sans-serif; font-weight: 700;">{{ $hrData['annual_summary']['overtime_hours'] ?? '48' }}</p>
                        <p class="text-xs text-gray-500" style="font-family: 'Poppins', sans-serif; font-weight: 400;">Hours this year</p>
                    </div>
                </div>
            </div>

            <!-- Bonus Earned -->
            <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-4 transition-all duration-200 hover:scale-105">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-sm font-medium text-gray-600" style="font-family: 'Poppins', sans-serif; font-weight: 500;">Bonus Earned</h4>
                        <p class="text-2xl font-bold text-gray-900" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Rp {{ number_format($hrData['annual_summary']['bonus_earned'] ?? 2500000, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500" style="font-family: 'Poppins', sans-serif; font-weight: 400;">Performance bonus</p>
                    </div>
                </div>
            </div>

            <!-- Training Hours -->
            <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 rounded-lg p-4 transition-all duration-200 hover:scale-105">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-sm font-medium text-gray-600" style="font-family: 'Poppins', sans-serif; font-weight: 500;">Training Hours</h4>
                        <p class="text-2xl font-bold text-gray-900" style="font-family: 'Poppins', sans-serif; font-weight: 700;">{{ $hrData['annual_summary']['training_hours'] ?? '32' }}</p>
                        <p class="text-xs text-gray-500" style="font-family: 'Poppins', sans-serif; font-weight: 400;">Hours completed</p>
                    </div>
                </div>
            </div>

            <!-- Projects Completed -->
            <div class="bg-gradient-to-r from-teal-50 to-teal-100 rounded-lg p-4 transition-all duration-200 hover:scale-105">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-sm font-medium text-gray-600" style="font-family: 'Poppins', sans-serif; font-weight: 500;">Projects Completed</h4>
                        <p class="text-2xl font-bold text-gray-900" style="font-family: 'Poppins', sans-serif; font-weight: 700;">{{ $hrData['annual_summary']['projects_completed'] ?? '12' }}</p>
                        <p class="text-xs text-gray-500" style="font-family: 'Poppins', sans-serif; font-weight: 400;">Successfully delivered</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Rating Section -->
        <div class="mt-6 bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-lg font-semibold text-gray-900" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Overall Performance Rating</h4>
                    <p class="text-sm text-gray-600" style="font-family: 'Poppins', sans-serif; font-weight: 400;">Based on annual review</p>
                </div>
                <div class="text-right">
                    <div class="flex items-center">
                        <span class="text-3xl font-bold text-yellow-600" style="font-family: 'Poppins', sans-serif; font-weight: 700;">{{ $hrData['annual_summary']['performance_rating'] ?? '4.2' }}</span>
                        <span class="text-lg text-gray-500 ml-1" style="font-family: 'Poppins', sans-serif; font-weight: 400;">/5.0</span>
                    </div>
                    <div class="flex items-center mt-1">
                        @php
                            $rating = $hrData['annual_summary']['performance_rating'] ?? 4.2;
                            $fullStars = floor($rating);
                            $hasHalfStar = ($rating - $fullStars) >= 0.5;
                        @endphp
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $fullStars)
                                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @elseif($i == $fullStars + 1 && $hasHalfStar)
                                <svg class="w-5 h-5 text-yellow-400" viewBox="0 0 20 20">
                                    <defs>
                                        <linearGradient id="half">
                                            <stop offset="50%" stop-color="currentColor"/>
                                            <stop offset="50%" stop-color="#e5e7eb"/>
                                        </linearGradient>
                                    </defs>
                                    <path fill="url(#half)" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-300 fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endif
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
