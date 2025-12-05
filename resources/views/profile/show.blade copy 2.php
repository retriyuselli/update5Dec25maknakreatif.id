@extends('layouts.app')

@section('title', 'Profile - ' . Auth::user()->name)

@section('content')
<style>
* {
    font-family: 'Poppins', sans-serif !important;
}
</style>

@include('front.header')

<div x-data="{ show: true }" class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-10" style="font-family: 'Poppins', sans-serif;">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-show="show" x-transition.opacity.duration.700ms>
        
        <!-- Success Alert -->
        @if(session('success'))
            <div 
                x-data="{ visible: true }" 
                x-show="visible" 
                x-transition.duration.500ms
                class="mb-6 flex items-center gap-2 bg-green-50 border border-green-300 text-green-700 px-4 py-3 rounded-xl shadow-sm">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
                <button @click="visible = false" class="ml-auto text-green-600 hover:text-green-800">&times;</button>
            </div>
        @endif

        <!-- Page Header -->
        <div class="mb-10 text-center" x-data x-init="$el.classList.add('animate-fade-in-down')">
            <h1 class="text-4xl font-bold text-gray-900 tracking-tight">Profile Dashboard</h1>
            <p class="text-gray-600 mt-2 text-lg font-medium">Kelola akun & data HR Anda di satu tempat</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Profile Section -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Profile Card -->
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden transform hover:scale-[1.01] transition duration-300">
                    @include('profile.sections.header')

                    <div class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            @include('profile.sections.personal-info')
                            @include('profile.sections.employment-info')
                        </div>
                    </div>
                </div>

                <!-- HR Performance -->
                <div class="bg-white rounded-2xl shadow-md p-4 border border-gray-100 transform hover:scale-[1.01] transition duration-300">
                    @include('profile.sections.performance-dashboard')
                </div>

                <!-- Annual Summary -->
                <div class="bg-white rounded-2xl shadow-md p-4 border border-gray-100 transform hover:scale-[1.01] transition duration-300">
                    @include('profile.sections.annual-summary')
                </div>

                <!-- Salary & Leave -->
                <div class="bg-white rounded-2xl shadow-md p-4 border border-gray-100 transform hover:scale-[1.01] transition duration-300">
                    @include('profile.sections.hr-salary-leave')
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Quick Actions -->
                <div x-data="{ open: true }" class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
                    <button @click="open = !open" class="w-full flex justify-between items-center p-4 font-semibold text-gray-700 hover:bg-gray-50">
                        Quick Actions 
                        <span x-text="open ? '−' : '+'"></span>
                    </button>
                    <div x-show="open" x-transition class="p-6">
                        @include('profile.sections.quick-actions')
                    </div>
                </div>

                <!-- HR Calendar -->
                <div class="bg-white rounded-2xl shadow-md p-4 border border-gray-100 transform hover:scale-[1.01] transition duration-300">
                    @include('profile.sections.upcoming-events')
                </div>

                <!-- Employee Benefits -->
                <div class="bg-white rounded-2xl shadow-md p-4 border border-gray-100 transform hover:scale-[1.01] transition duration-300">
                    @include('profile.sections.employee-benefits')
                </div>
            </div>
        </div>
    </div>
</div>

@include('profile.sections.scripts')

<!-- Tambahan animasi Tailwind dan Poppins Font -->
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');

* {
    font-family: 'Poppins', sans-serif !important;
}

body {
    font-family: 'Poppins', sans-serif !important;
}

@keyframes fade-in-down {
  0% { opacity: 0; transform: translateY(-20px); }
  100% { opacity: 1; transform: translateY(0); }
}

.animate-fade-in-down { 
    animation: fade-in-down 0.7s ease-out; 
}

/* Enhanced font weights for Poppins */
.font-light { font-weight: 300 !important; }
.font-normal { font-weight: 400 !important; }
.font-medium { font-weight: 500 !important; }
.font-semibold { font-weight: 600 !important; }
.font-bold { font-weight: 700 !important; }
.font-extrabold { font-weight: 800 !important; }
</style>
@endsection