<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Operasional - IKRA System</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/locales/id.global.min.js"></script>

    <style>
        /* Penyesuaian agar tampilan FullCalendar serasi dengan Tailwind */
        .fc { --fc-border-color: #e2e8f0; font-family: ui-sans-serif, system-ui, sans-serif; }
        .fc .fc-col-header-cell-cushion { padding: 8px 4px; font-weight: 600; color: #475569; font-size: 0.875rem; text-transform: uppercase; }
        .fc .fc-toolbar-title { font-size: 1.25rem !important; font-weight: 700; color: #0f172a; }
        .fc .fc-button-primary { background-color: #0f172a !important; border-color: #0f172a !important; font-size: 0.875rem; font-weight: 600; border-radius: 0.5rem !important; }
        .fc .fc-button-primary:hover { background-color: #1e293b !important; }
        .fc .fc-button-primary:disabled { background-color: #cbd5e1 !important; border-color: #cbd5e1 !important; opacity: 0.7; }
        .fc .fc-daygrid-day-number { font-size: 0.875rem; font-weight: 500; color: #64748b; padding: 6px !important; }
        .fc .fc-event { border-radius: 6px; padding: 2px 6px; font-size: 0.75rem; font-weight: 600; border: none !important; cursor: pointer; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-800">

    <div class="flex min-h-screen">
        <div class="w-64 bg-slate-900 text-white p-6 space-y-6 flex flex-col justify-between shrink-0">
            <div class="space-y-6">
                <div class="flex items-center space-x-3 border-b border-slate-700 pb-4">
                    <div class="bg-emerald-500 p-2 rounded-lg text-slate-900 font-bold">IK</div>
                    <div>
                        <h1 class="font-bold text-lg leading-tight">IKRA System</h1>
                        <p class="text-xs text-slate-400">Tim Operasional</p>
                    </div>
                </div>
                
                <nav class="space-y-2">
                    <a href="{{ route('operational.dashboard') }}" class="flex items-center space-x-3 {{ request()->routeIs('operational.dashboard') ? 'bg-emerald-600 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} p-3 rounded-lg transition">
                        <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                        <span>Dashboard</span>
                    </a>
                    
                    <a href="{{ route('operational.schedule') }}" class="flex items-center space-x-3 {{ request()->routeIs('operational.schedule') ? 'bg-emerald-600 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} p-3 rounded-lg transition">
                        <i class="fa-solid fa-calendar-days w-5 text-center"></i>
                        <span>Agenda & Jadwal</span>
                    </a>

                    <a href="{{ route('operational.pencairan') }}" class="flex items-center space-x-3 {{ request()->routeIs('operational.pencairan') ? 'bg-emerald-600 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} p-3 rounded-lg transition">
                        <i class="fa-solid fa-hand-holding-dollar w-5 text-center"></i>
                        <span>Pencairan Dana Ekstra</span>
                    </a>

                    <a href="{{ route('operational.penyaluran-reguler.index') }}" class="flex items-center space-x-3 {{ request()->routeIs('operational.penyaluran-reguler.index') ? 'bg-emerald-600 text-white font-medium' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} p-3 rounded-lg transition">
                        <i class="fa-solid fa-heart-circle-check w-5 text-center"></i>
                        <span>Penyaluran Infak Reguler</span>
                    </a>
                </nav>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="border-t border-slate-700 pt-4">
                @csrf
                <button type="submit" class="w-full flex items-center space-x-3 text-red-400 hover:bg-slate-800 p-3 rounded-lg transition text-left cursor-pointer">
                    <i class="fa-solid fa-right-from-bracket w-5 text-center"></i>
                    <span>Keluar Sistem</span>
                </button>
            </form>
        </div>

        <div class="flex-1 p-10 space-y-8">
            <div class="p-6 bg-white border border-slate-200 rounded-2xl shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Selamat Datang</h1>
                    <p class="text-sm text-slate-500 mt-1">Pantau ringkasan statistik program kerja dan kelola timeline agenda eksekusi lapangan secara.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm flex items-center space-x-4">
                    <div class="bg-slate-100 p-3.5 rounded-xl text-xl">🎁</div>
                    <div>
                        <span class="text-xs text-slate-400 font-bold block uppercase tracking-wider">Donasi Umum</span>
                        <span class="text-xl font-black text-slate-800">{{ $totalDonasi }} Program</span>
                    </div>
                </div>
                <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm flex items-center space-x-4">
                    <div class="bg-purple-50 p-3.5 rounded-xl text-xl">🎙️</div>
                    <div>
                        <span class="text-xs text-purple-400 font-bold block uppercase tracking-wider">Podcast IKRA</span>
                        <span class="text-xl font-black text-purple-900">{{ $totalPodcast }} Program</span>
                    </div>
                </div>
                <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm flex items-center space-x-4">
                    <div class="bg-blue-50 p-3.5 rounded-xl text-xl">🎬</div>
                    <div>
                        <span class="text-xs text-blue-400 font-bold block uppercase tracking-wider">Cinema Edukasi</span>
                        <span class="text-xl font-black text-blue-900">{{ $totalCinema }} Program</span>
                    </div>
                </div>
                <div class="bg-amber-50 border border-amber-200 p-5 rounded-2xl shadow-sm flex items-center space-x-4">
                    <div class="bg-amber-500/10 text-amber-700 p-3.5 rounded-xl text-xl">⏳</div>
                    <div>
                        <span class="text-xs text-amber-600 font-bold block uppercase tracking-wider">Belum Terjadwal</span>
                        <span class="text-xl font-black text-amber-800">{{ $jadwalPending }} Agenda</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-4 gap-6 items-start">
                <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">🎨 Indikator Warna Agenda</h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-4 h-4 bg-slate-900 rounded-md shrink-0"></div>
                            <span class="text-xs font-semibold text-slate-600">Donasi Umum</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-4 h-4 bg-purple-500 rounded-md shrink-0"></div>
                            <span class="text-xs font-semibold text-slate-600">Program Podcast</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-4 h-4 bg-blue-500 rounded-md shrink-0"></div>
                            <span class="text-xs font-semibold text-slate-600">Cinema Edukasi</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-4 h-4 bg-emerald-500 rounded-md shrink-0"></div>
                            <span class="text-xs font-semibold text-slate-600">Penyaluran Infak Reguler</span>
                        </div>
                    </div>
                </div>

                <div class="xl:col-span-3 bg-white border border-slate-200 p-6 rounded-2xl shadow-sm">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            
            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'id', // Set bahasa ke Indonesia
                initialView: 'dayGridMonth', // Tampilan standar bulanan
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                editable: false,
                selectable: true,
                events: "{{ route('operational.calendar.events') }}", // Panggil Route JSON yang sudah kita buat
                
                // Aksi ketika nama agenda diklik oleh tim operasional
                eventClick: function(info) {
                    var deskripsi = info.event.extendedProps.detail ? info.event.extendedProps.detail : 'Tidak ada deskripsi tambahan.';
                    alert(
                        "📌 NAMA AGENDA:\n" + info.event.title + "\n\n" +
                        "📂 KATEGORI TIPE:\nProgram " + info.event.extendedProps.tipe + "\n\n" +
                        "📝 DETAIL KETERANGAN:\n" + deskripsi
                    );
                }
            });
            
            calendar.render();
        });
    </script>
</body>
</html>