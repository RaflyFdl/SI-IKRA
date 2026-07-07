<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>IKRA - Mock Payment Simulator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at 10% 20%, rgb(17, 24, 39) 0%, rgb(9, 13, 22) 90.2%);
        }
        .text-outfit {
            font-family: 'Outfit', sans-serif;
        }
        /* Mobile Glassmorphism Mockup */
        .phone-mockup {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
            border: 12px solid #1f2937;
            border-radius: 40px;
            background: #0f172a;
        }
        .notch {
            width: 140px;
            height: 25px;
            background: #1f2937;
            border-bottom-left-radius: 18px;
            border-bottom-right-radius: 18px;
            margin: 0 auto;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            top: 0;
            z-index: 10;
        }
        /* Keypad UI for ATM */
        .atm-key {
            background: linear-gradient(135deg, #4b5563 0%, #1f2937 100%);
            box-shadow: inset 0 2px 2px rgba(255,255,255,0.2), 0 4px 6px rgba(0,0,0,0.4);
            border: 1px solid #374151;
            transition: all 0.1s ease;
        }
        .atm-key:active {
            transform: translateY(2px);
            box-shadow: inset 0 1px 1px rgba(0,0,0,0.5), 0 1px 2px rgba(0,0,0,0.2);
        }
        /* Glass card design */
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        /* ATM screen filter effect */
        .atm-screen {
            background: radial-gradient(circle, #0c2018 0%, #040c09 100%);
            border: 4px solid #374151;
            box-shadow: inset 0 0 20px rgba(0, 255, 0, 0.15);
        }
        .atm-green-text {
            color: #10b981;
            text-shadow: 0 0 4px rgba(16, 185, 129, 0.5);
            font-family: 'Courier New', Courier, monospace;
        }
    </style>
</head>
<body class="min-h-screen text-slate-100 flex flex-col items-center justify-between pb-8">

    <!-- Header disclaimer banner -->
    <header class="w-full max-w-6xl px-4 mt-6">
        <div class="glass-card rounded-2xl p-5 border-l-4 border-amber-500 shadow-lg relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="absolute right-0 top-0 opacity-5 pointer-events-none transform translate-x-12 -translate-y-8">
                <i class="fa-solid fa-triangle-exclamation text-[150px] text-amber-500"></i>
            </div>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500 text-xl shrink-0">
                    <i class="fa-solid fa-triangle-exclamation animate-pulse"></i>
                </div>
                <div>
                    <h1 class="text-md font-bold text-amber-400 text-outfit font-sans">MODE SIMULASI & SIDANG HANYA UNTUK KEPERLUAN DEMO</h1>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Halaman ini dibuat khusus untuk memicu callback pembayaran Xendit Sandbox secara visual tanpa memerlukan uang sungguhan.
                    </p>
                </div>
            </div>
            <a href="{{ route('member.dashboard') }}" class="text-xs font-semibold bg-emerald-600/20 hover:bg-emerald-600 text-emerald-400 hover:text-white px-4 py-2.5 rounded-xl border border-emerald-500/30 transition flex items-center gap-2 self-start md:self-center">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>
    </header>

    <!-- Main Container -->
    <main class="w-full max-w-5xl px-4 my-8 flex flex-col lg:flex-row gap-12 items-center justify-center">

        <!-- Left Column: Controls & Dynamic State Info -->
        <div class="w-full lg:w-1/2 space-y-6">
            <div class="space-y-2">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-500">Gateway Sandbox Simulator</span>
                <h2 class="text-3xl font-extrabold text-white text-outfit leading-tight font-sans">Simulator ATM & Mobile Banking</h2>
                <p class="text-sm text-slate-400">
                    Silakan salin nomor Virtual Account (VA) yang didapatkan saat mendaftar anggota atau saat melakukan checkout program infak ekstra, lalu tempel di sini untuk mensimulasikan pembayaran transfer.
                </p>
            </div>

            <!-- Mode Selector -->
            <div class="p-1.5 bg-slate-900/90 rounded-2xl border border-slate-800 flex gap-2">
                <button onclick="switchMode('mbanking')" id="btn-mbanking" class="flex-1 py-3 px-4 rounded-xl text-sm font-semibold transition-all flex items-center justify-center gap-2 bg-emerald-600 text-white shadow-md">
                    <i class="fa-solid fa-mobile-screen-button"></i> Mobile Banking
                </button>
                <button onclick="switchMode('atm')" id="btn-atm" class="flex-1 py-3 px-4 rounded-xl text-sm font-semibold transition-all flex items-center justify-center gap-2 text-slate-400 hover:text-slate-200">
                    <i class="fa-solid fa-money-bill-transfer"></i> Layanan ATM
                </button>
            </div>

            <!-- Info Card (Glass) -->
            <div class="glass-card rounded-2xl p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <span class="text-xs font-bold text-slate-400 uppercase">Deteksi Virtual Account</span>
                    <span id="va-status-badge" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-slate-400">Belum Dicek</span>
                </div>
                <div class="space-y-3">
                    <div class="grid grid-cols-3 gap-2 text-xs">
                        <span class="text-slate-500 font-medium">Jenis VA</span>
                        <span id="info-type" class="col-span-2 text-slate-300 font-semibold">-</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 text-xs">
                        <span class="text-slate-500 font-medium">Nama Pemilik</span>
                        <span id="info-name" class="col-span-2 text-slate-300 font-semibold">-</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 text-xs">
                        <span class="text-slate-500 font-medium">Bank Tujuan</span>
                        <span id="info-bank" class="col-span-2 text-slate-300 font-semibold">-</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 text-xs">
                        <span class="text-slate-500 font-medium">Keterangan</span>
                        <span id="info-details" class="col-span-2 text-slate-300 font-semibold">-</span>
                    </div>
                    <div id="extra-amount-info" class="grid grid-cols-3 gap-2 text-xs hidden">
                        <span class="text-slate-500 font-medium">Nominal Tetap</span>
                        <span id="info-amount" class="col-span-2 text-emerald-400 font-bold font-mono">-</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Visual Simulators -->
        <div class="w-full lg:w-1/2 flex items-center justify-center">

            <!-- MOBILE BANKING SIMULATOR -->
            <div id="sim-mbanking" class="w-[350px] phone-mockup relative overflow-hidden flex flex-col transition-all duration-300">
                <div class="notch"></div>
                
                <!-- Status bar -->
                <div class="h-10 pt-4 px-6 flex justify-between items-center text-[10px] font-semibold text-slate-400 z-0">
                    <span>11:40</span>
                    <div class="flex gap-1.5 items-center">
                        <i class="fa-solid fa-signal"></i>
                        <i class="fa-solid fa-wifi"></i>
                        <i class="fa-solid fa-battery-three-quarters text-xs"></i>
                    </div>
                </div>

                <!-- App Header -->
                <div class="px-6 py-4 flex items-center justify-between bg-gradient-to-r from-[#0284c7] to-[#0f172a] border-b border-sky-950">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center text-white text-xs">
                            <i class="fa-solid fa-bank text-sky-300"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-white tracking-wide">IKRA Mobile</h3>
                            <p class="text-[9px] text-sky-300">Sandbox App</p>
                        </div>
                    </div>
                    <span class="text-[9px] bg-emerald-500 text-white font-bold px-2 py-0.5 rounded-full">Active Connection</span>
                </div>

                <!-- App Body -->
                <div class="p-6 flex-1 space-y-5 bg-[#0b0f19]">
                    
                    <!-- Visa/Mastercard style card -->
                    <div class="bg-gradient-to-br from-slate-800 to-slate-950 p-4 rounded-2xl border border-slate-700 relative overflow-hidden shadow-md">
                        <div class="absolute right-4 top-4 text-slate-700/30 text-4xl">
                            <i class="fa-solid fa-network-wired"></i>
                        </div>
                        <p class="text-[9px] text-slate-500 uppercase tracking-widest">Saldo Virtual</p>
                        <h4 class="text-lg font-bold font-mono text-white mt-1">Rp 99.999.999</h4>
                        <div class="flex justify-between items-end mt-4">
                            <div>
                                <p class="text-[8px] text-slate-500">MEMBER DUMMY</p>
                                <p class="text-[9px] text-slate-400 font-semibold font-mono">**** 5678</p>
                            </div>
                            <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="MasterCard Logo" class="h-6 opacity-80">
                        </div>
                    </div>

                    <!-- M-Banking Form -->
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Nomor Virtual Account</label>
                            <div class="relative">
                                <input type="text" id="mb-va" oninput="handleVaInput(this.value)" placeholder="Tempel VA Xendit di sini..." class="w-full bg-slate-900 border border-slate-800 rounded-xl py-2.5 pl-3 pr-10 text-xs text-white focus:outline-none focus:border-sky-500 font-mono font-semibold transition">
                                <span class="absolute right-3 top-2.5 text-slate-600">
                                    <i class="fa-solid fa-receipt"></i>
                                </span>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Nominal Transfer</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-xs text-slate-500 font-bold">Rp</span>
                                <input type="number" id="mb-amount" placeholder="Contoh: 100000" class="w-full bg-slate-900 border border-slate-800 rounded-xl py-2.5 pl-8 pr-3 text-xs text-white font-mono font-bold focus:outline-none focus:border-sky-500 transition">
                            </div>
                            <p class="text-[9px] text-slate-500" id="mb-amount-help">
                                Nominal dapat diedit secara bebas untuk Infak Reguler (Minimal Rp 10.000).
                            </p>
                        </div>

                        <!-- Proceed Button -->
                        <button type="button" onclick="submitPayment()" class="w-full bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold py-3 rounded-xl transition flex items-center justify-center gap-2 shadow-lg shadow-sky-600/20 mt-2">
                            <i class="fa-solid fa-paper-plane"></i> Kirim Transfer (Simulasi)
                        </button>
                    </div>

                </div>

                <!-- Home Indicator Bar -->
                <div class="h-6 pb-2 flex items-center justify-center bg-[#0b0f19]">
                    <div class="w-28 h-1 bg-slate-700 rounded-full"></div>
                </div>
            </div>

            <!-- ATM SIMULATOR -->
            <div id="sim-atm" class="w-full max-w-[430px] p-6 bg-slate-800/80 border border-slate-700 rounded-3xl shadow-2xl flex flex-col gap-6 transition-all duration-300 hidden">
                
                <!-- ATM Screen Container -->
                <div class="atm-screen rounded-xl p-6 aspect-[4/3] flex flex-col justify-between text-xs relative overflow-hidden select-none">
                    
                    <!-- Screen Overlay Lines -->
                    <div class="absolute inset-0 pointer-events-none bg-[linear-gradient(rgba(18,16,16,0)_50%,_rgba(0,0,0,0.25)_50%),_linear-gradient(90deg,_rgba(255,0,0,0.06),_rgba(0,255,0,0.02),_rgba(0,0,255,0.06))] bg-[size:100%_4px,_6px_100%] opacity-15"></div>

                    <!-- Screen Header -->
                    <div class="flex justify-between items-start border-b border-emerald-950 pb-2">
                        <div class="atm-green-text text-[9px] uppercase tracking-wider font-bold">ATM Bersama IKRA v1.0</div>
                        <div class="atm-green-text text-[9px] font-bold">SANDBOX ACTIVE</div>
                    </div>

                    <!-- Screen Content area -->
                    <div class="my-auto space-y-4">
                        <div class="text-center">
                            <p class="atm-green-text font-bold text-sm tracking-widest uppercase">TRANSFER VIRTUAL ACCOUNT</p>
                        </div>

                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="atm-green-text text-[10px]">NOMOR VA:</span>
                                <span id="atm-screen-va" class="atm-green-text font-bold text-[13px] tracking-wide font-mono">_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="atm-green-text text-[10px]">NOMINAL :</span>
                                <span id="atm-screen-amount" class="atm-green-text font-bold text-[13px] tracking-wide font-mono">Rp 0</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="atm-green-text text-[10px]">NAMA    :</span>
                                <span id="atm-screen-name" class="atm-green-text font-bold text-[10px] tracking-wide font-mono">-</span>
                            </div>
                        </div>

                        <div id="atm-screen-msg" class="text-center py-1 border border-dashed border-emerald-800 rounded bg-emerald-950/20">
                            <span class="atm-green-text text-[9px] animate-pulse">SILAKAN MASUKKAN NOMOR VA PADA KEYPAD DI BAWAH...</span>
                        </div>
                    </div>

                    <!-- Screen Footer Menu Layout (Right aligned button cues) -->
                    <div class="flex justify-between items-end border-t border-emerald-950 pt-2 text-[9px] font-bold">
                        <span class="atm-green-text">BENAR -> ENTER</span>
                        <span class="atm-green-text">SALAH -> CLEAR</span>
                    </div>

                </div>

                <!-- ATM Physical Keypad Area -->
                <div class="grid grid-cols-4 gap-4 p-4 bg-slate-900 rounded-2xl border border-slate-700">
                    
                    <!-- Left: Numpad -->
                    <div class="col-span-3 grid grid-cols-3 gap-3">
                        <button onclick="pressAtm('1')" class="atm-key text-white font-bold py-3 rounded-lg text-sm">1</button>
                        <button onclick="pressAtm('2')" class="atm-key text-white font-bold py-3 rounded-lg text-sm">2</button>
                        <button onclick="pressAtm('3')" class="atm-key text-white font-bold py-3 rounded-lg text-sm">3</button>

                        <button onclick="pressAtm('4')" class="atm-key text-white font-bold py-3 rounded-lg text-sm">4</button>
                        <button onclick="pressAtm('5')" class="atm-key text-white font-bold py-3 rounded-lg text-sm">5</button>
                        <button onclick="pressAtm('6')" class="atm-key text-white font-bold py-3 rounded-lg text-sm">6</button>

                        <button onclick="pressAtm('7')" class="atm-key text-white font-bold py-3 rounded-lg text-sm">7</button>
                        <button onclick="pressAtm('8')" class="atm-key text-white font-bold py-3 rounded-lg text-sm">8</button>
                        <button onclick="pressAtm('9')" class="atm-key text-white font-bold py-3 rounded-lg text-sm">9</button>

                        <button class="atm-key text-white font-bold py-3 rounded-lg text-sm" disabled>&nbsp;</button>
                        <button onclick="pressAtm('0')" class="atm-key text-white font-bold py-3 rounded-lg text-sm">0</button>
                        <button class="atm-key text-white font-bold py-3 rounded-lg text-sm" disabled>&nbsp;</button>
                    </div>

                    <!-- Right: Control Keys -->
                    <div class="flex flex-col gap-3 justify-between">
                        <button onclick="pressAtm('cancel')" class="bg-rose-600 hover:bg-rose-500 border border-rose-700 text-white font-bold py-3 rounded-lg text-[10px] uppercase shadow-lg">Batal</button>
                        <button onclick="pressAtm('clear')" class="bg-amber-500 hover:bg-amber-400 border border-amber-600 text-slate-900 font-bold py-3 rounded-lg text-[10px] uppercase shadow-lg">Hapus</button>
                        <button onclick="pressAtm('enter')" class="bg-emerald-600 hover:bg-emerald-500 border border-emerald-700 text-white font-bold py-4 rounded-lg text-[10px] uppercase shadow-lg flex-1">Benar</button>
                    </div>

                </div>

            </div>

        </div>

    </main>

    <!-- Footer -->
    <footer class="text-xs text-slate-500 text-center mt-6">
        &copy; 2026 SI-Infak IKRA • Xendit Integration sandbox test environment
    </footer>

    <!-- Scripting Area -->
    <script>
        // Global State variables
        let currentMode = 'mbanking'; // 'mbanking' or 'atm'
        let vaType = null; // 'reguler' or 'ekstra'
        let vaExists = false;
        let vaName = '-';
        let vaBank = '-';
        let vaAmount = 0;
        let vaDetails = '-';
        let isPaid = false;

        // ATM state specifically
        let atmVa = '';
        let atmAmount = '';
        let atmActiveField = 'va'; // 'va' or 'amount'

        // CSRF Token setup for Fetch
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Switch screen mode (m-Banking vs ATM)
        function switchMode(mode) {
            currentMode = mode;
            const btnMb = document.getElementById('btn-mbanking');
            const btnAtm = document.getElementById('btn-atm');
            const simMb = document.getElementById('sim-mbanking');
            const simAtm = document.getElementById('sim-atm');

            if (mode === 'mbanking') {
                btnMb.className = "flex-1 py-3 px-4 rounded-xl text-sm font-semibold transition-all flex items-center justify-center gap-2 bg-emerald-600 text-white shadow-md";
                btnAtm.className = "flex-1 py-3 px-4 rounded-xl text-sm font-semibold transition-all flex items-center justify-center gap-2 text-slate-400 hover:text-slate-200";
                simMb.classList.remove('hidden');
                simAtm.classList.add('hidden');
            } else {
                btnAtm.className = "flex-1 py-3 px-4 rounded-xl text-sm font-semibold transition-all flex items-center justify-center gap-2 bg-emerald-600 text-white shadow-md";
                btnMb.className = "flex-1 py-3 px-4 rounded-xl text-sm font-semibold transition-all flex items-center justify-center gap-2 text-slate-400 hover:text-slate-200";
                simAtm.classList.remove('hidden');
                simMb.classList.add('hidden');
                
                // Copy m-banking input state over to ATM state to make it seamless
                const mbVaValue = document.getElementById('mb-va').value;
                if (mbVaValue) {
                    atmVa = mbVaValue.replace(/\D/g, '');
                    updateAtmScreen();
                    checkVirtualAccount(atmVa);
                }
            }
        }

        // Check VA dynamically from server
        let lookupTimeout = null;
        function handleVaInput(vaNum) {
            clearTimeout(lookupTimeout);
            
            // Clean value (only digits)
            const cleanVa = vaNum.replace(/\D/g, '');
            
            if (cleanVa.length >= 8) {
                lookupTimeout = setTimeout(() => {
                    checkVirtualAccount(cleanVa);
                }, 500);
            } else {
                resetInfo();
            }
        }

        function checkVirtualAccount(vaNumber) {
            const statusBadge = document.getElementById('va-status-badge');
            statusBadge.innerText = 'Mengecek...';
            statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-500 animate-pulse';

            fetch(`/simulator/search-va?va_number=${vaNumber}`)
                .then(res => res.json())
                .then(data => {
                    if (data.exists) {
                        vaExists = true;
                        vaType = data.type;
                        vaName = data.name;
                        vaBank = data.bank;
                        vaAmount = data.amount;
                        vaDetails = data.details;
                        isPaid = data.is_paid || false;

                        // Display in Info Card
                        statusBadge.innerText = 'VA Ditemukan';
                        statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-500';

                        document.getElementById('info-type').innerText = (vaType === 'reguler') ? 'Infak Reguler (Open VA)' : 'Infak Ekstra (Closed VA)';
                        document.getElementById('info-name').innerText = vaName;
                        document.getElementById('info-bank').innerText = vaBank;
                        document.getElementById('info-details').innerText = vaDetails;

                        const amountInfoDiv = document.getElementById('extra-amount-info');
                        const mbAmountInput = document.getElementById('mb-amount');
                        const mbAmountHelp = document.getElementById('mb-amount-help');

                        if (vaType === 'ekstra') {
                            amountInfoDiv.classList.remove('hidden');
                            document.getElementById('info-amount').innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(vaAmount);
                            
                            // Locked input on mobile screen
                            mbAmountInput.value = vaAmount;
                            mbAmountInput.readOnly = true;
                            mbAmountInput.classList.add('bg-slate-950', 'text-slate-400');
                            mbAmountHelp.innerHTML = `<span class="text-amber-500 font-bold"><i class="fa-solid fa-lock"></i> VA Ekstra Terkunci.</span> Pembayaran harus bernilai pas.`;
                            
                            if (isPaid) {
                                statusBadge.innerText = 'Sudah Lunas';
                                statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/10 text-blue-500';
                            }
                        } else {
                            // Reguler (Open VA)
                            amountInfoDiv.classList.add('hidden');
                            
                            mbAmountInput.value = '';
                            mbAmountInput.readOnly = false;
                            mbAmountInput.classList.remove('bg-slate-950', 'text-slate-400');
                            mbAmountHelp.innerText = 'Nominal dapat diedit secara bebas untuk Infak Reguler (Minimal Rp 10.000).';
                        }

                        // Also sync ATM UI if in ATM Mode
                        if (currentMode === 'atm') {
                            updateAtmScreen();
                            if (vaType === 'ekstra') {
                                atmAmount = vaAmount.toString();
                                atmActiveField = 'va'; // Keep active on Enter
                                document.getElementById('atm-screen-msg').innerHTML = `<span class="atm-green-text text-[9px] text-amber-500 font-bold">VA EKSTRA TERDETEKSI: TEKAN BENAR UNTUK TRANSFER</span>`;
                            } else {
                                atmActiveField = 'amount'; // Prompt user to enter amount in ATM screen
                                document.getElementById('atm-screen-msg').innerHTML = `<span class="atm-green-text text-[9px] animate-pulse">MASUKKAN NOMINAL LALU TEKAN ENTER / BENAR</span>`;
                            }
                            updateAtmScreen();
                        }

                    } else {
                        resetInfo();
                        statusBadge.innerText = 'VA Tidak Ditemukan';
                        statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-500';
                        
                        if (currentMode === 'atm') {
                            document.getElementById('atm-screen-msg').innerHTML = `<span class="atm-green-text text-[9px] text-rose-500 font-bold">NOMOR VIRTUAL ACCOUNT TIDAK TERDAFTAR</span>`;
                        }
                    }
                })
                .catch(err => {
                    console.error('Lookup Error:', err);
                    resetInfo();
                    statusBadge.innerText = 'Error API';
                    statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-500';
                });
        }

        function resetInfo() {
            vaExists = false;
            vaType = null;
            vaName = '-';
            vaBank = '-';
            vaAmount = 0;
            vaDetails = '-';
            isPaid = false;

            document.getElementById('info-type').innerText = '-';
            document.getElementById('info-name').innerText = '-';
            document.getElementById('info-bank').innerText = '-';
            document.getElementById('info-details').innerText = '-';
            document.getElementById('extra-amount-info').classList.add('hidden');

            const mbAmountInput = document.getElementById('mb-amount');
            mbAmountInput.readOnly = false;
            mbAmountInput.classList.remove('bg-slate-950', 'text-slate-400');
            document.getElementById('mb-amount-help').innerText = 'Nominal dapat diedit secara bebas untuk Infak Reguler (Minimal Rp 10.000).';
        }

        // ATM Numpad click logic
        function pressAtm(key) {
            if (key === 'cancel') {
                atmVa = '';
                atmAmount = '';
                atmActiveField = 'va';
                resetInfo();
                document.getElementById('mb-va').value = '';
                updateAtmScreen();
                document.getElementById('atm-screen-msg').innerHTML = `<span class="atm-green-text text-[9px] animate-pulse">TRANSAKSI BATAL. SILAKAN MASUKKAN NOMOR VA...</span>`;
                return;
            }

            if (key === 'clear') {
                if (atmActiveField === 'va') {
                    atmVa = atmVa.slice(0, -1);
                } else {
                    // For reguler VA, allow deleting amount
                    if (vaType === 'reguler') {
                        atmAmount = atmAmount.slice(0, -1);
                    }
                }
                updateAtmScreen();
                return;
            }

            if (key === 'enter') {
                if (atmActiveField === 'va') {
                    if (!vaExists) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Virtual Account Salah',
                            text: 'Masukkan nomor Virtual Account yang terdaftar terlebih dahulu.'
                        });
                        return;
                    }
                    if (vaType === 'reguler' && atmAmount === '') {
                        atmActiveField = 'amount';
                        document.getElementById('atm-screen-msg').innerHTML = `<span class="atm-green-text text-[9px] animate-pulse font-bold">MASUKKAN NOMINAL DAN TEKAN BENAR</span>`;
                        updateAtmScreen();
                    } else {
                        // Ready to pay!
                        processSimulation(atmVa, atmAmount || vaAmount, vaType);
                    }
                } else if (atmActiveField === 'amount') {
                    const amountVal = parseInt(atmAmount);
                    if (isNaN(amountVal) || amountVal < 10000) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Nominal Kurang',
                            text: 'Minimal pembayaran Infak Reguler adalah Rp 10.000'
                        });
                        return;
                    }
                    processSimulation(atmVa, amountVal, 'reguler');
                }
                return;
            }

            // Standard digit inputs
            if (atmActiveField === 'va') {
                atmVa += key;
                updateAtmScreen();
                handleVaInput(atmVa);
            } else if (atmActiveField === 'amount' && vaType === 'reguler') {
                atmAmount += key;
                updateAtmScreen();
            }
        }

        // Draw dynamic string inputs to screen mockup of ATM
        function updateAtmScreen() {
            // Update VA field visualization
            let displayVa = atmVa;
            if (displayVa === '') {
                displayVa = '_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _';
            }
            document.getElementById('atm-screen-va').innerText = displayVa;

            // Update Amount field visualization
            let finalAmt = 'Rp 0';
            if (vaType === 'ekstra') {
                finalAmt = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(vaAmount);
            } else if (atmAmount !== '') {
                finalAmt = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(parseInt(atmAmount));
            }
            document.getElementById('atm-screen-amount').innerText = finalAmt;

            // Update Name field visualization
            document.getElementById('atm-screen-name').innerText = vaName !== '-' ? vaName : '-';
        }

        // Submission logic for Mobile Banking
        function submitPayment() {
            const vaInput = document.getElementById('mb-va').value;
            const amountInput = document.getElementById('mb-amount').value;

            if (!vaExists) {
                Swal.fire({
                    icon: 'error',
                    title: 'VA Tidak Valid',
                    text: 'Virtual Account tidak terdaftar atau belum terdeteksi sistem lokal.'
                });
                return;
            }

            if (isPaid && vaType === 'ekstra') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Sudah Lunas',
                    text: 'Infaq ekstra ini sudah dilunasi sebelumnya.'
                });
                return;
            }

            const amtVal = parseInt(amountInput);
            if (isNaN(amtVal) || amtVal < 10000) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Nominal Tidak Sah',
                    text: 'Minimal transfer adalah Rp 10.000'
                });
                return;
            }

            processSimulation(vaInput, amtVal, vaType);
        }

        // Shared AJAX simulation dispatch to Xendit Endpoint API
        function processSimulation(va, amount, type) {
            Swal.fire({
                title: 'Memproses Transfer...',
                text: 'Menghubungkan ke API Xendit Sandbox. Mohon tunggu.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('/simulator/pay', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    va_number: va,
                    amount: amount,
                    type: type
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Pembayaran Sukses!',
                        text: data.message,
                        confirmButtonText: 'Bagus',
                        confirmButtonColor: '#059669'
                    }).then(() => {
                        // Reset simulator page state
                        if (currentMode === 'atm') {
                            pressAtm('cancel');
                        } else {
                            document.getElementById('mb-va').value = '';
                            document.getElementById('mb-amount').value = '';
                            resetInfo();
                            document.getElementById('va-status-badge').innerText = 'Belum Dicek';
                            document.getElementById('va-status-badge').className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-slate-400';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Pembayaran Gagal',
                        text: data.message
                    });
                }
            })
            .catch(err => {
                console.error('Payment processing failed:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Koneksi Eror',
                    text: 'Gagal terhubung dengan server lokal atau API Xendit terputus.'
                });
            });
        }
    </script>
</body>
</html>
