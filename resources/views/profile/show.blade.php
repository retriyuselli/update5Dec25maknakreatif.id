@extends('layouts.app')

@section('title', 'Profile - ' . Auth::user()->name)

@section('content')
<!-- Include Header -->
@include('front.header')

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Success Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard Profil</h1>
            <p class="text-gray-600 mt-2">Kelola informasi akun dan data HR Anda</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Profile Information Card -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <!-- Profile Header -->
                    @include('profile.sections.header')

                    <!-- Profile Details -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Personal Information -->
                            @include('profile.sections.personal-info')

                            <!-- Employment Information -->
                            @include('profile.sections.employment-info')
                        </div>
                    </div>
                </div>

                <!-- HR Performance Dashboard -->
                {{-- @include('profile.sections.performance-dashboard') --}}

                <!-- HR Salary & Leave Information -->
                @include('profile.sections.hr-salary-leave')
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                @include('profile.sections.quick-actions')

                <!-- HR Calendar -->
                @include('profile.sections.upcoming-events')
            </div>
        </div>
    </div>
</div>

@include('profile.sections.scripts')
@include('front.footer')

@endsection