<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>429 - Terlalu Banyak Permintaan | SIAKAD SMPN 2 Subang</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600,700,800,900&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4 font-sans">
    <div class="max-w-md w-full bg-slate-800/90 border border-slate-700 rounded-2xl shadow-2xl p-6 sm:p-8 text-center backdrop-blur-sm">
        <div class="w-16 h-16 bg-amber-500/10 text-amber-400 border border-amber-500/30 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl shadow-inner">
            🛡️
        </div>
        
        <span class="inline-block px-3 py-1 bg-amber-500/20 text-amber-300 text-xs font-mono font-bold rounded-full mb-3 border border-amber-500/30">
            HTTP 429 • TOO MANY REQUESTS
        </span>

        <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight mb-2">
            Proteksi Anti-DDoS Aktif
        </h1>

        <p class="text-xs sm:text-sm text-slate-300 leading-relaxed mb-6">
            Perangkat atau jaringan Anda mengirimkan terlalu banyak permintaan dalam waktu singkat. Sistem membatasi akses sementara demi menjaga kestabilan server bagi seluruh pengguna.
        </p>

        <div class="p-3.5 bg-slate-900/80 rounded-xl border border-slate-700/80 text-xs text-slate-400 mb-6 flex items-center justify-between">
            <span>Silakan coba lagi dalam:</span>
            <span id="countdown" class="font-mono font-bold text-amber-400 text-sm">60 detik</span>
        </div>

        <div class="flex items-center justify-center gap-3">
            <button onclick="window.location.reload()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg transition shadow-sm cursor-pointer">
                Muat Ulang Halaman
            </button>
            <a href="/" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-slate-200 font-bold text-xs rounded-lg transition border border-slate-600">
                Beranda
            </a>
        </div>

        <p class="text-[11px] text-slate-500 mt-6">
            SMP Negeri 2 Subang • Sistem Informasi Akademik & E-Learning
        </p>
    </div>

    <script>
        let seconds = 60;
        const countdownEl = document.getElementById('countdown');
        const timer = setInterval(() => {
            seconds--;
            if (seconds <= 0) {
                clearInterval(timer);
                countdownEl.innerText = "Sekarang";
            } else {
                countdownEl.innerText = seconds + " detik";
            }
        }, 1000);
    </script>
</body>
</html>
