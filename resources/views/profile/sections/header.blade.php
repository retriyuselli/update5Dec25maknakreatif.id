<!-- Profile Header Section -->
<div class="px-6 py-8" style="background: linear-gradient(to right, #2563eb, #1e40af); font-family: 'Poppins', sans-serif;">
    <div class="flex items-center space-x-6">
        <div class="relative group">
            @if(Auth::user()->avatar_url)
                <img class="h-24 w-24 rounded-full object-cover border-4 border-white shadow-lg transition-transform duration-300 group-hover:scale-105"
                    src="{{ Storage::url(Auth::user()->avatar_url) }}"
                    alt="Profile {{ Auth::user()->name }}"
                    onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=ffffff&background=1e40af&size=128&font-size=0.4'">
            @else
                <img class="h-24 w-24 rounded-full object-cover border-4 border-white shadow-lg transition-transform duration-300 group-hover:scale-105"
                    src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=ffffff&background=1e40af&size=128&font-size=0.4"
                    alt="Profile {{ Auth::user()->name }}">
            @endif
            <div class="absolute -bottom-1 -right-1 h-6 w-6 border-2 border-white rounded-full animate-pulse" style="background-color: #4ade80;"></div>
        </div>
        <div class="text-white flex-1">
            <h2 class="text-2xl font-bold tracking-tight" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                {{ Auth::user()->name }}
            </h2>
            <p class="font-medium mt-1" style="color: #bfdbfe; font-family: 'Poppins', sans-serif; font-weight: 500;">
                {{ Auth::user()->email }}
            </p>
            <div class="mt-3 flex items-center space-x-4">
                <span class="px-3 py-1 rounded-full text-sm font-medium transition-all duration-200" style="background-color: rgba(59, 130, 246, 0.5); color: white; font-family: 'Poppins', sans-serif;">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-4 0v2"></path>
                    </svg>
                    ID: #WO{{ str_pad(Auth::user()->id, 4, '0', STR_PAD_LEFT) }}
                </span>
                <span class="px-3 py-1 rounded-full text-sm font-medium transition-all duration-200" style="background-color: rgba(34, 197, 94, 0.5); color: white; font-family: 'Poppins', sans-serif;">
                    <svg class="w-3 h-3 inline mr-1 animate-pulse" fill="currentColor" viewBox="0 0 20 20" style="color: #86efac;">
                        <circle cx="10" cy="10" r="10"/>
                    </svg>
                    Aktif
                </span>
            </div>
        </div>
        <div class="hidden md:flex flex-col items-end space-y-2">
            <div class="text-right" style="color: #bfdbfe;">
                <p class="text-xs font-medium" style="font-family: 'Poppins', sans-serif;">Profil Diperbarui</p>
                <p class="text-sm font-semibold" style="font-family: 'Poppins', sans-serif;">
                    {{ Auth::user()->updated_at->diffForHumans() }}
                </p>
            </div>
        </div>
    </div>
</div>
