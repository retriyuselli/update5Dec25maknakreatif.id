@extends('layouts.app')

@section('title', 'Pendaftaran Prospect WOFINS')

@section('content')
    @include('front.header')
    <div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-8">
                <p class="text-sm text-gray-600 mb-2">Hubungi kami</p>
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                    Diskusikan kebutuhan bisnis<br>
                    Anda dengan kami
                </h1>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 max-w-6xl mx-auto">
                <!-- Form Section -->
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <form action="{{ route('prospect-app.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div>
                            <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Lengkap
                            </label>
                            <input id="full_name" name="full_name" type="text" required value="{{ old('full_name') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('full_name') border-red-300 @enderror"
                                placeholder="e.g. John Doe">
                            @error('full_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Perusahaan
                            </label>
                            <input id="company_name" name="company_name" type="text" required
                                value="{{ old('company_name') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('company_name') border-red-300 @enderror"
                                placeholder="e.g. PT Sukses Maju Makmur">
                            @error('company_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="industry_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Departemen
                            </label>
                            <select id="industry_id" name="industry_id" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('industry_id') border-red-300 @enderror appearance-none bg-white">
                                <option value="">Pilih departemen</option>
                                @foreach ($industries as $industry)
                                    <option value="{{ $industry->id }}"
                                        {{ old('industry_id') == $industry->id ? 'selected' : '' }}>
                                        {{ $industry->industry_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('industry_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="user_size" class="block text-sm font-medium text-gray-700 mb-2">
                                Jumlah Karyawan
                            </label>
                            <select id="user_size" name="user_size" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('user_size') border-red-300 @enderror appearance-none bg-white">
                                <option value="">Pilih jumlah karyawan</option>
                                <option value="1-10" {{ old('user_size') == '1-10' ? 'selected' : '' }}>1-10 Karyawan
                                </option>
                                <option value="11-50" {{ old('user_size') == '11-50' ? 'selected' : '' }}>11-50 Karyawan
                                </option>
                                <option value="51-200" {{ old('user_size') == '51-200' ? 'selected' : '' }}>51-200 Karyawan
                                </option>
                                <option value="201-500" {{ old('user_size') == '201-500' ? 'selected' : '' }}>201-500
                                    Karyawan</option>
                                <option value="501-1000" {{ old('user_size') == '501-1000' ? 'selected' : '' }}>501-1000
                                    Karyawan</option>
                                <option value="1000+" {{ old('user_size') == '1000+' ? 'selected' : '' }}>Lebih dari 1000
                                    Karyawan</option>
                            </select>
                            @error('user_size')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor Ponsel
                            </label>
                            <input id="phone" name="phone" type="tel" required value="{{ old('phone') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('phone') border-red-300 @enderror"
                                placeholder="e.g. 081122334455">
                            @error('phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Alamat Email
                            </label>
                            <input id="email" name="email" type="email" required value="{{ old('email') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('email') border-red-300 @enderror"
                                placeholder="e.g. john@company.com">
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="reason_for_interest" class="block text-sm font-medium text-gray-700 mb-2">
                                Kebutuhan dan Tantangan Bisnis
                            </label>
                            <textarea id="reason_for_interest" name="reason_for_interest" rows="4" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('reason_for_interest') border-red-300 @enderror"
                                placeholder="Ceritakan kebutuhan dan tantangan bisnis Anda...">{{ old('reason_for_interest') }}</textarea>
                            @error('reason_for_interest')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Hidden fields for compatibility -->
                        <input type="hidden" name="position" value="Decision Maker">
                        <input type="hidden" name="service" value="standard">
                        <input type="hidden" name="notes"
                            value="Form submitted via consultation page - interested in Standard Package">

                        <div class="flex items-center">
                            <input id="terms" name="terms" type="checkbox" required
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="terms" class="ml-2 block text-sm text-gray-700">
                                Saya pengambil keputusan dalam pembelian software
                            </label>
                        </div>

                        <!-- reCAPTCHA placeholder -->
                        <div class="bg-gray-100 p-4 rounded-lg flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-6 h-6 border-2 border-gray-400 rounded mr-3"></div>
                                <span class="text-sm text-gray-600">I'm not a robot</span>
                            </div>
                            <div class="text-xs text-gray-500">
                                <div>reCAPTCHA</div>
                                <div>Privacy - Terms</div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 shadow-lg hover:shadow-xl">
                            Jadwalkan meeting
                        </button>

                        <div class="text-sm text-gray-600">
                            Dengan klik tombol jadwalkan meeting, saya menyetujui
                            <a href="#" class="text-blue-600 hover:underline">syarat & ketentuan</a>
                            serta <a href="#" class="text-blue-600 hover:underline">pernyataan privasi</a> Wofins.
                        </div>

                        <!-- Error Display -->
                        @if ($errors->any())
                            <div class="mt-6 bg-red-50 border border-red-200 rounded-lg p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan pada form:</h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <ul class="list-disc list-inside space-y-1">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>

                <!-- Contact Sidebar -->
                <div class="space-y-6">
                    <div class="text-center lg:text-left">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">
                            Beritahu kami, apa yang bisa kami bantu?
                        </h3>
                    </div>

                    <!-- Live Chat -->
                    <div class="bg-white rounded-xl p-6 shadow-lg">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 mb-2">Live chat</h4>
                                <p class="text-gray-600 text-sm mb-4">
                                    Tanya langsung tentang fitur dan penawaran harga terbaik.
                                </p>
                                <a href="#"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z">
                                        </path>
                                    </svg>
                                    WhatsApp sales
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Kontak Sales -->
                    <div class="bg-white rounded-xl p-6 shadow-lg">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-pink-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-pink-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 mb-2">Kontak sales</h4>
                                <p class="text-gray-600 text-sm mb-2">
                                    Konsultasikan masalah bisnis Anda dan dapatkan solusi terbaik.
                                </p>
                                <p class="text-gray-500 text-xs mb-4">Office hours: 08:00 - 17:00 WIB</p>
                                <a href="tel:1500069"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z">
                                        </path>
                                    </svg>
                                    1500 069
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Tim Support -->
                    <div class="bg-white rounded-xl p-6 shadow-lg">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 mb-2">Hubungi tim support</h4>
                                <p class="text-gray-600 text-sm mb-4">
                                    Hubungi tim support untuk bantuan teknis penggunaan produk.
                                </p>
                                <a href="#"
                                    class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 text-sm font-medium rounded-lg hover:bg-blue-200 transition-colors">
                                    Hubungi sekarang
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                        </path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Section with City Cards -->
            <div class="mt-16">
                <div class="max-w-4xl mx-auto">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2 text-center">
                        Wofins hadir di berbagai kota di Indonesia
                    </h3>
                    <p class="text-gray-600 text-center mb-8">
                        Untuk memudahkan layanan konsultasi kami dan lebih dekat dengan Anda.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Medan -->
                        <div class="relative rounded-xl overflow-hidden h-32 bg-gradient-to-r from-blue-600 to-blue-800">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <h4 class="text-white text-xl font-semibold">Medan</h4>
                            </div>
                        </div>

                        <!-- Yogyakarta -->
                        <div
                            class="relative rounded-xl overflow-hidden h-32 bg-gradient-to-r from-orange-600 to-orange-800">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <h4 class="text-white text-xl font-semibold">Yogyakarta</h4>
                            </div>
                        </div>

                        <!-- Pekanbaru -->
                        <div class="relative rounded-xl overflow-hidden h-32 bg-gradient-to-r from-green-600 to-green-800">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <h4 class="text-white text-xl font-semibold">Pekanbaru</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Contact Section -->
            <div class="mt-16 text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">
                    Alami kendala pemakaian Wofins By Makna?<br>
                    Hubungi tim support kami!
                </h3>

                <div class="mt-8">
                    <div class="inline-block bg-white rounded-xl p-6 shadow-lg">
                        <h4 class="font-semibold text-gray-900 mb-2">WhatsApp channel</h4>
                        <p class="text-gray-600 text-sm mb-4">Chat langsung dengan tim support</p>
                        <a href="https://wa.me/6281373183794" target="_blank"
                            class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
                            </svg>
                            WhatsApp sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('front.footer')
@endsection
