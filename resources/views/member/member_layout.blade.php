<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Portal Anggota IKRA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <nav class="bg-[#0b6e3f] text-white sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/10 backdrop-blur-md rounded-xl flex items-center justify-center font-bold text-lg border border-white/20">
                    I
                </div>
                <div>
                    <span class="font-bold block text-sm leading-tight tracking-tight">Portal Anggota IKRA</span>
                    <span class="text-[11px] text-emerald-200 font-medium block">Yayasan Wakaf Padjadjaran</span>
                </div>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="hidden md:block text-right">
                    <span class="text-xs block font-semibold text-emerald-100">Selamat Datang,</span>
                    <span class="text-sm block font-bold text-white">{{ Auth::user()->name ?? 'Anggota IKRA' }}</span>
                </div>
                
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-emerald-800/80 hover:bg-red-700 border border-emerald-700 hover:border-red-600 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all duration-200">
                        🚪 Keluar
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-10 space-y-8">
        
        <div class="flex border-b border-gray-200 overflow-x-auto bg-white rounded-t-2xl px-4 pt-2 shadow-xs gap-2">
            <a href="/dashboard" class="py-3.5 px-5 font-bold text-sm transition-all rounded-t-xl whitespace-nowrap {{ Request::is('dashboard') && !Request::is('dashboard/infak-ekstra') && !Request::is('dashboard/profil') ? 'text-[#0b6e3f] border-b-2 border-[#0b6e3f] bg-emerald-50/40' : 'text-gray-400 hover:text-gray-700' }}">
                💳 1. Tentang Infak Reguler
            </a>
            <a href="/dashboard/infak-ekstra" class="py-3.5 px-5 font-bold text-sm transition-all rounded-t-xl whitespace-nowrap {{ Request::is('dashboard/infak-ekstra') ? 'text-[#0b6e3f] border-b-2 border-[#0b6e3f] bg-emerald-50/40' : 'text-gray-400 hover:text-gray-700' }}">
                🌟 2. Jelajah Infak Ekstra
            </a>
            <a href="/dashboard/profil" class="py-3.5 px-5 font-bold text-sm transition-all rounded-t-xl whitespace-nowrap {{ Request::is('dashboard/profil') ? 'text-[#0b6e3f] border-b-2 border-[#0b6e3f] bg-emerald-50/40' : 'text-gray-400 hover:text-gray-700' }}">
                👤 3. Pengaturan Profil
            </a>
        </div>

        <div class="transition-all duration-300">
            @yield('member_content')
        </div>

    </main>

</body>
</html>