@extends('layouts.app')

@section('title', 'Register - WOFINS')

@section('content')
    <div class="min-h-screen flex items-center justify-center bg-blue-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-5xl mx-auto bg-white rounded-2xl shadow-xl p-6 sm:p-8">
            <div class="grid md:grid-cols-2 gap-8 items-stretch">
                <div class="rounded-xl overflow-hidden">
                    <img src="{{ asset('images/team_makna.jpg') }}" alt="Illustration"
                        class="w-full h-full object-cover object-center">
                </div>
                <div class="flex items-center">
                    <div class="w-full max-w-md mx-auto">
                        <h1 class="text-3xl font-bold text-gray-900">Create an account</h1>
                        <p class="mt-1 text-sm text-gray-500">Join us.</p>
                        <form class="mt-8 space-y-6" action="{{ route('front.register') }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="text-sm font-medium text-gray-900">Full Name</label>
                                    <input id="name" name="name" type="text" autocomplete="name" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Enter your full name" value="{{ old('name') }}">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-gray-900">Email</label>
                                    <input id="email" name="email" type="email" autocomplete="email" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="Enter your email" value="{{ old('email') }}">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div x-data="{ show: false }">
                                    <label class="text-sm font-medium text-gray-900">Password</label>
                                    <div class="mt-1 relative">
                                        <input id="password" name="password" :type="show ? 'text' : 'password'"
                                            autocomplete="new-password" required
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Enter your password">
                                        <button type="button" @click="show = !show"
                                            class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600">
                                            <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M2.036 12.322a1.012 1.012 0 010-.644C3.423 7.51 7.36 4.5 12 4.5c4.64 0 8.577 3.01 9.964 7.178.07.21.07.434 0 .644C20.577 16.49 16.64 19.5 12 19.5c-4.64 0-8.577-3.01-9.964-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <svg x-show="show" class="h-5 w-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M3.98 8.223A10.477 10.477 0 002.036 12.32c-.07.21-.07.434 0 .644C3.423 16.49 7.36 19.5 12 19.5c1.676 0 3.257-.31 4.679-.873M6.115 6.115C8.011 4.904 9.93 4.5 12 4.5c4.64 0 8.577 3.01 9.964 7.178.07.21.07.434 0 .644a10.495 10.495 0 01-1.606 2.472M3 3l18 18M9.88 9.88a3 3 0 104.24 4.24" />
                                            </svg>
                                        </button>
                                    </div>
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div x-data="{ show: false }">
                                    <label class="text-sm font-medium text-gray-900">Confirm Password</label>
                                    <div class="mt-1 relative">
                                        <input id="password_confirmation" name="password_confirmation"
                                            :type="show ? 'text' : 'password'" autocomplete="new-password" required
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            placeholder="Re-enter your password">
                                        <button type="button" @click="show = !show"
                                            class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600">
                                            <svg x-show="!show" class="h-5 w-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M2.036 12.322a1.012 1.012 0 010-.644C3.423 7.51 7.36 4.5 12 4.5c4.64 0 8.577 3.01 9.964 7.178.07.21.07.434 0 .644C20.577 16.49 16.64 19.5 12 19.5c-4.64 0-8.577-3.01-9.964-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <svg x-show="show" class="h-5 w-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M3.98 8.223A10.477 10.477 0 002.036 12.32c-.07.21-.07.434 0 .644C3.423 16.49 7.36 19.5 12 19.5c1.676 0 3.257-.31 4.679-.873M6.115 6.115C8.011 4.904 9.93 4.5 12 4.5c4.64 0 8.577 3.01 9.964 7.178.07.21.07.434 0 .644a10.495 10.495 0 01-1.606 2.472M3 3l18 18M9.88 9.88a3 3 0 104.24 4.24" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <button type="submit"
                                    class="w-full px-4 py-2 rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Sign
                                    Up</button>
                                <a href="{{ route('front.login') }}"
                                    class="w-full inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Already
                                    have an account?</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
