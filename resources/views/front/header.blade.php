<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wedding Organizer</title>

    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Poppins', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont',
                            'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'Noto Sans', 'sans-serif'
                        ],
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>

<body class="font-sans">
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2">
                        <img class="h-6 md:h-8 w-auto" src="{{ route('brand.logo') }}" alt="Logo">
                    </a>
                </div>

                <!-- Desktop Navigation Menu -->
                <div class="hidden md:block">
                    <div class="ml-6 flex items-baseline">
                        <!-- Fitur Dropdown -->
                        <div class="relative group">
                            <a href="#"
                                class="text-gray-700 ml-7 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition duration-300 flex items-center">
                                Fitur
                                <svg class="ml-1 h-4 w-4 transform group-hover:rotate-180 transition-transform duration-200"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </a>

                            <!-- Fitur Dropdown Menu -->
                            <div
                                class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform translate-y-1 group-hover:translate-y-0">
                                <a href="{{ route('front.invoice') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600 transition duration-150">Invoice</a>
                                <a href="{{ route('front.biaya_feature') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600 transition duration-150">Biaya</a>
                                <a href="{{ route('front.laporan_feature') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600 transition duration-150">Laporan</a>
                                <a href="{{ route('front.aset_feature') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600 transition duration-150">Aset
                                    Tetap</a>
                                <a href="#"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600 transition duration-150">Inventory</a>
                                <a href="{{ route('front.hris_feature') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600 transition duration-150">HRIS</a>
                                <a href="{{ route('front.payroll_feature') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-blue-600 transition duration-150">Payroll</a>
                            </div>
                        </div>
                        <a href="{{ route('harga') }}"
                            class="text-gray-700 ml-7 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition duration-300">
                            Harga
                        </a>
                        <a href="{{ route('blog') }}"
                            class="text-gray-700 ml-7 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition duration-300">
                            Blog
                        </a>
                        @guest
                            <a href="{{ route('front.login') }}"
                                class="text-gray-700 ml-7 hover:text-blue-600 px-4 py-2 rounded-md text-sm font-medium transition duration-300">
                                Login
                            </a>
                        @endguest
                        @auth
                            <a href="{{ route('profile') }}"
                                class="text-gray-700 ml-7 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition duration-300 {{ request()->routeIs('profile') ? 'text-blue-600 bg-blue-50' : '' }}">
                                Profile
                            </a>
                            <a href="{{ route('dashboard') }}"
                                class="text-gray-700 ml-7 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition duration-300 {{ request()->routeIs('dashboard') ? 'text-blue-600 bg-blue-50' : '' }}">
                                Dashboard
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Right Side -->
                <div class="ml-6 flex items-center space-x-4">
                    @auth
                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <button @click="open = !open"
                                class="flex items-center space-x-2 text-gray-700 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-full">
                                <div class="relative">
                                    <img class="h-8 w-8 rounded-full object-cover border-2 border-gray-300 shadow-sm hover:border-blue-400 transition-all duration-200"
                                        src="{{ Storage::url(Auth::user()->avatar_url) ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&color=ffffff&background=1e40af&size=128' }}"
                                        alt="Profile {{ Auth::user()->name }}"
                                        onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=ffffff&background=1e40af&size=128'">
                                    <div
                                        class="absolute -bottom-0 -right-0 h-3 w-3 bg-green-400 border-2 border-white rounded-full">
                                    </div>
                                </div>
                                <span class="hidden md:block text-sm font-medium">{{ Auth::user()->name }}</span>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" @click.away="open = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
                                <a href="{{ route('dashboard') }}"
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                                    </svg>
                                    Dashboard
                                </a>
                                <a href="{{ route('profile') }}"
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Profile
                                </a>
                                <hr class="my-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="flex items-center w-full px-4 py-2 text-sm text-gray-800 hover:bg-gray-100">
                                        <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                            </path>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Login & Sign Up Buttons -->
                        <div class="flex items-center space-x-3">
                            {{-- <a href="{{ route('front.login') }}"
                                class="text-gray-700 hover:text-blue-600 px-4 py-2 rounded-md text-sm font-medium transition duration-300">
                                Login
                            </a> --}}
                            <a href="{{ route('pendaftaran') }}"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-300 shadow-md hover:shadow-lg">
                                Coba Gratis
                            </a>
                        </div>
                    @endauth

                    <!-- Mobile menu button -->
                    <div class="md:hidden" x-data="{ mobileOpen: false }">
                        <button @click="mobileOpen = !mobileOpen"
                            class="text-gray-700 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 p-2">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>

                        <!-- Mobile Menu -->
                        <div x-show="mobileOpen" @click.away="mobileOpen = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute top-16 right-4 left-4 bg-white rounded-lg shadow-lg py-2 z-50 border border-gray-200"
                            x-data="{ mobileFiturOpen: false }">

                            <!-- Fitur Dropdown for Mobile -->
                            <div>
                                <button @click="mobileFiturOpen = !mobileFiturOpen"
                                    class="flex items-center justify-between w-full px-4 py-2 text-gray-700 hover:bg-gray-100">
                                    <span>Fitur</span>
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="mobileFiturOpen" class="pl-4 bg-gray-50">
                                    <a href="#"
                                        class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-100">Invoice</a>
                                    <a href="#"
                                        class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-100">Vendor</a>
                                    <a href="#"
                                        class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-100">Biaya</a>
                                    <a href="#"
                                        class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-100">Laporan</a>
                                    <a href="#"
                                        class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-100">Aset Tetap</a>
                                    <a href="#"
                                        class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-100">Inventory</a>
                                    <a href="#"
                                        class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-100">HRIS</a>
                                    <a href="#"
                                        class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-100">Payroll</a>
                                </div>
                            </div>

                            <a href="{{ route('harga') }}"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Harga</a>
                            <a href="{{ route('blog') }}"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Blog</a>
                            @auth
                                <a href="{{ route('profile') }}"
                                    class="block px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('profile') ? 'bg-blue-50 text-blue-600' : '' }}">Profile</a>
                                <a href="{{ route('dashboard') }}"
                                    class="block px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : '' }}">Dashboard</a>
                            @endauth
                            {{-- <a href="{{ route('vendor') }}"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('vendor') ? 'bg-blue-50 text-blue-600' : '' }}">Vendor</a>
                            @if ($hasProjectAccess)
                                <a href="{{ route('project') }}"
                                    class="block px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('project') ? 'bg-blue-50 text-blue-600' : '' }}">Project</a>
                            @endif
                            <a href="{{ route('anggota') }}"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('anggota') ? 'bg-blue-50 text-blue-600' : '' }}">Anggota</a>
                            <a href="{{ route('kontak') }}"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('kontak') ? 'bg-pink-50 text-pink-600' : '' }}">Kontak</a> --}}

                            @guest
                                <hr class="my-2">
                                <a href="{{ route('front.login') }}"
                                    class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Login</a>
                                <a href="{{ route('pendaftaran') }}"
                                    class="block px-4 py-2 text-blue-600 hover:bg-blue-50 font-medium">Coba Gratis</a>
                            @endguest
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>
</body>

</html>
