<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>iseller — Website Kasir UMKM</title>
        <meta
            name="description"
            content="iseller membantu kasir UMKM: pencatatan transaksi, produk, laporan penjualan, dan integrasi pembayaran Midtrans. Ada fitur Premium juga."/>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>

    <body class="bg-slate-950 text-slate-100">
        <!-- Background glow -->
        <div class="pointer-events-none fixed inset-0 overflow-hidden">
            <div
                class="absolute -top-40 left-1/2 h-[520px] w-[520px] -translate-x-1/2 rounded-full bg-indigo-500/20 blur-3xl"></div>
            <div
                class="absolute top-40 -left-40 h-[520px] w-[520px] rounded-full bg-cyan-500/10 blur-3xl"></div>
        </div>

        <!-- Header -->
        <header class="relative z-10">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5">
                <a href="#" class="flex items-center gap-2">
                    <span
                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-white/10 ring-1 ring-white/10">
                        <span class="text-lg font-bold text-white">i</span>
                    </span>
                    <span class="text-lg font-semibold tracking-tight">iseller</span>
                </a>

                <div class="hidden items-center gap-8 md:flex">
                    <a href="#fitur" class="text-sm text-slate-300 hover:text-white">Fitur</a>
                    <a href="#midtrans" class="text-sm text-slate-300 hover:text-white">Midtrans</a>
                    <a href="#premium" class="text-sm text-slate-300 hover:text-white">Premium</a>
                    <a href="#faq" class="text-sm text-slate-300 hover:text-white">FAQ</a>
                </div>

                <div class="flex items-center gap-3">
                    <a
                        href="/login"
                        class="hidden rounded-xl px-4 py-2 text-sm text-slate-200 ring-1 ring-white/10 hover:bg-white/5 md:inline-block">
                        Login
                    </a>
                    <a
                        href="#cta"
                        class="rounded-xl bg-indigo-500 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-400">
                        Coba Gratis
                    </a>
                </div>
            </nav>
        </header>

        <!-- Hero -->
        <main class="relative z-10">
            <section class="mx-auto max-w-7xl px-6 pb-16 pt-10 md:pb-24 md:pt-16">
                <div class="grid items-center gap-10 md:grid-cols-2">
                    <div>
                        <p
                            class="inline-flex items-center gap-2 rounded-full bg-white/5 px-3 py-1 text-xs text-slate-200 ring-1 ring-white/10">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                            Dibuat untuk kasir UMKM — cepat, rapi, dan siap naik kelas
                        </p>

                        <h1
                            class="mt-5 text-4xl font-bold leading-tight tracking-tight text-white md:text-5xl">
                            Kelola kasir UMKM lebih mudah dengan
                            <span class="text-indigo-300">iseller</span>
                        </h1>

                        <p class="mt-4 text-base leading-relaxed text-slate-300 md:text-lg">
                            Catat transaksi, kelola produk, pantau stok, dan lihat laporan penjualan dalam
                            satu dashboard. Sudah terintegrasi pembayaran online via
                            <span class="text-slate-100 font-medium">Midtrans</span>.
                        </p>

                        <div class="mt-7 flex flex-col gap-3 sm:flex-row sm:items-center">
                            <a
                                href="#cta"
                                class="inline-flex items-center justify-center rounded-xl bg-indigo-500 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-400">
                                Mulai Coba Gratis
                            </a>
                            <a
                                href="#fitur"
                                class="inline-flex items-center justify-center rounded-xl bg-white/5 px-5 py-3 text-sm font-semibold text-slate-100 ring-1 ring-white/10 hover:bg-white/10">
                                Lihat Fitur
                            </a>
                        </div>

                        <div class="mt-6 flex flex-wrap items-center gap-3 text-xs text-slate-400">
                            <span class="rounded-full bg-white/5 px-3 py-1 ring-1 ring-white/10">⚡ Cepat dipakai</span>
                            <span class="rounded-full bg-white/5 px-3 py-1 ring-1 ring-white/10">🔐 Data lebih aman</span>
                            <span class="rounded-full bg-white/5 px-3 py-1 ring-1 ring-white/10">📈 Laporan otomatis</span>
                        </div>
                    </div>

                    <!-- Mockup -->
                    <div class="relative">
                        <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="h-3 w-3 rounded-full bg-red-400/80"></span>
                                    <span class="h-3 w-3 rounded-full bg-yellow-400/80"></span>
                                    <span class="h-3 w-3 rounded-full bg-emerald-400/80"></span>
                                </div>
                                <span class="text-xs text-slate-400">Dashboard</span>
                            </div>

                            <div class="mt-4 grid gap-3 md:grid-cols-2">
                                <div class="rounded-xl bg-slate-900/60 p-4 ring-1 ring-white/10">
                                    <p class="text-xs text-slate-400">Pendapatan Hari Ini</p>
                                    <p class="mt-2 text-2xl font-bold">Rp 2.450.000</p>
                                    <p class="mt-1 text-xs text-emerald-300">+12% dari kemarin</p>
                                </div>

                                <div class="rounded-xl bg-slate-900/60 p-4 ring-1 ring-white/10">
                                    <p class="text-xs text-slate-400">Transaksi</p>
                                    <p class="mt-2 text-2xl font-bold">86</p>
                                    <p class="mt-1 text-xs text-slate-400">Rata-rata 1–2 menit</p>
                                </div>

                                <div class="rounded-xl bg-slate-900/60 p-4 ring-1 ring-white/10 md:col-span-2">
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs text-slate-400">Produk Terlaris</p>
                                        <p class="text-xs text-slate-500">Minggu ini</p>
                                    </div>
                                    <div class="mt-3 space-y-2">
                                        <div
                                            class="flex items-center justify-between rounded-lg bg-white/5 px-3 py-2 ring-1 ring-white/10">
                                            <span class="text-sm">Kopi Susu Gula Aren</span>
                                            <span class="text-xs text-slate-300">124x</span>
                                        </div>
                                        <div
                                            class="flex items-center justify-between rounded-lg bg-white/5 px-3 py-2 ring-1 ring-white/10">
                                            <span class="text-sm">Roti Bakar Coklat</span>
                                            <span class="text-xs text-slate-300">98x</span>
                                        </div>
                                        <div
                                            class="flex items-center justify-between rounded-lg bg-white/5 px-3 py-2 ring-1 ring-white/10">
                                            <span class="text-sm">Es Teh</span>
                                            <span class="text-xs text-slate-300">90x</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 rounded-xl bg-indigo-500/10 p-4 ring-1 ring-indigo-400/20">
                                <p class="text-sm font-semibold text-indigo-200">Midtrans Ready ✅</p>
                                <p class="mt-1 text-xs text-slate-300">Terima pembayaran online (VA, e-wallet, QRIS) langsung dari iseller.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Trusted / stats -->
            <section class="mx-auto max-w-7xl px-6 pb-16">
                <div
                    class="grid gap-4 rounded-2xl bg-white/5 p-6 ring-1 ring-white/10 md:grid-cols-3">
                    <div class="rounded-xl bg-slate-900/40 p-4 ring-1 ring-white/10">
                        <p class="text-xs text-slate-400">Setup cepat</p>
                        <p class="mt-1 text-2xl font-bold text-white">&lt; 10 menit</p>
                        <p class="mt-2 text-sm text-slate-300">Mulai jualan, input produk, dan siap transaksi.</p>
                    </div>
                    <div class="rounded-xl bg-slate-900/40 p-4 ring-1 ring-white/10">
                        <p class="text-xs text-slate-400">Ringkas & akurat</p>
                        <p class="mt-1 text-2xl font-bold text-white">Laporan otomatis</p>
                        <p class="mt-2 text-sm text-slate-300">Rekap harian, mingguan, bulanan tanpa ribet.</p>
                    </div>
                    <div class="rounded-xl bg-slate-900/40 p-4 ring-1 ring-white/10">
                        <p class="text-xs text-slate-400">Skalabel</p>
                        <p class="mt-1 text-2xl font-bold text-white">Siap Premium</p>
                        <p class="mt-2 text-sm text-slate-300">Tambahkan fitur saat bisnismu tumbuh.</p>
                    </div>
                </div>
            </section>

            <!-- Features -->
            <section id="fitur" class="mx-auto max-w-7xl px-6 pb-16 md:pb-24">
                <div class="flex items-end justify-between gap-6">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight text-white">Fitur utama iseller</h2>
                        <p class="mt-2 max-w-2xl text-slate-300">
                            Fokus ke kebutuhan kasir UMKM: transaksi cepat, data rapi, dan mudah dipantau.
                        </p>
                    </div>
                </div>

                <div class="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-2xl bg-white/5 p-6 ring-1 ring-white/10 hover:bg-white/10">
                        <p class="text-sm font-semibold">🧾 POS Kasir</p>
                        <p class="mt-2 text-sm text-slate-300">Input transaksi cepat, diskon, catatan pembeli, dan cetak struk.</p>
                    </div>
                    <div class="rounded-2xl bg-white/5 p-6 ring-1 ring-white/10 hover:bg-white/10">
                        <p class="text-sm font-semibold">📦 Produk & Stok</p>
                        <p class="mt-2 text-sm text-slate-300">Kelola produk, kategori, harga, dan stok agar tidak salah hitung.</p>
                    </div>
                    <div class="rounded-2xl bg-white/5 p-6 ring-1 ring-white/10 hover:bg-white/10">
                        <p class="text-sm font-semibold">📊 Laporan Penjualan</p>
                        <p class="mt-2 text-sm text-slate-300">Lihat omzet, jumlah transaksi, produk terlaris, dan tren penjualan.</p>
                    </div>
                    <div class="rounded-2xl bg-white/5 p-6 ring-1 ring-white/10 hover:bg-white/10">
                        <p class="text-sm font-semibold">👥 Multi User</p>
                        <p class="mt-2 text-sm text-slate-300">Atur role admin/kasir, pembatasan akses, dan audit sederhana.</p>
                    </div>
                    <div class="rounded-2xl bg-white/5 p-6 ring-1 ring-white/10 hover:bg-white/10">
                        <p class="text-sm font-semibold">🧠 Mudah Dipakai</p>
                        <p class="mt-2 text-sm text-slate-300">Tampilan sederhana, cocok untuk pemula dan operasional harian.</p>
                    </div>
                    <div class="rounded-2xl bg-white/5 p-6 ring-1 ring-white/10 hover:bg-white/10">
                        <p class="text-sm font-semibold">🔐 Keamanan Data</p>
                        <p class="mt-2 text-sm text-slate-300">Validasi transaksi, histori, dan kontrol akses untuk mengurangi error.</p>
                    </div>
                </div>
            </section>

            <!-- Midtrans -->
            <section id="midtrans" class="mx-auto max-w-7xl px-6 pb-16 md:pb-24">
                <div class="grid items-center gap-8 md:grid-cols-2">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight text-white">Terintegrasi Midtrans</h2>
                        <p class="mt-3 text-slate-300">
                            iseller mendukung integrasi pembayaran via Midtrans supaya UMKM bisa menerima
                            pembayaran online dengan lebih fleksibel.
                        </p>

                        <ul class="mt-6 space-y-3 text-sm text-slate-300">
                            <li class="flex gap-3">
                                <span
                                    class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500/15 ring-1 ring-emerald-500/30">✓</span>
                                <span>Dukungan channel populer (mis: VA, e-wallet, QRIS) sesuai konfigurasi Midtrans</span>
                            </li>
                            <li class="flex gap-3">
                                <span
                                    class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500/15 ring-1 ring-emerald-500/30">✓</span>
                                <span>Status pembayaran otomatis (pending/settlement/expire/cancel)</span>
                            </li>
                            <li class="flex gap-3">
                                <span
                                    class="mt-0.5 inline-flex h-5 w-5 items-center justify-center rounded-full bg-emerald-500/15 ring-1 ring-emerald-500/30">✓</span>
                                <span>Notifikasi via webhook untuk sinkronisasi transaksi</span>
                            </li>
                        </ul>
                    </div>

                    <div class="rounded-2xl bg-white/5 p-6 ring-1 ring-white/10">
                        <p class="text-sm font-semibold text-white">Alur Pembayaran (ringkas)</p>
                        <div class="mt-4 space-y-3 text-sm text-slate-300">
                            <div class="rounded-xl bg-slate-900/50 p-4 ring-1 ring-white/10">
                                <p class="font-medium text-slate-100">1) Kasir buat invoice</p>
                                <p class="mt-1">Sistem membuat order dan menampilkan opsi pembayaran.</p>
                            </div>
                            <div class="rounded-xl bg-slate-900/50 p-4 ring-1 ring-white/10">
                                <p class="font-medium text-slate-100">2) Midtrans proses pembayaran</p>
                                <p class="mt-1">Pembeli bayar via metode yang tersedia.</p>
                            </div>
                            <div class="rounded-xl bg-slate-900/50 p-4 ring-1 ring-white/10">
                                <p class="font-medium text-slate-100">3) Status otomatis masuk ke iseller</p>
                                <p class="mt-1">Transaksi terupdate real-time lewat notifikasi.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Pricing -->
            <section id="premium" class="mx-auto max-w-7xl px-6 pb-16 md:pb-24">
                <div class="flex flex-col gap-2">
                    <h2 class="text-3xl font-bold tracking-tight text-white">Paket & Fitur Premium</h2>
                    <p class="max-w-2xl text-slate-300">
                        Mulai dari kebutuhan dasar, lalu upgrade ke Premium saat butuh fitur lebih
                        lengkap.
                    </p>
                </div>

                <div class="mt-8 grid gap-4 lg:grid-cols-3">
                    <!-- Free -->
                    <div class="rounded-2xl bg-white/5 p-6 ring-1 ring-white/10">
                        <p class="text-sm font-semibold text-white">Starter</p>
                        <p class="mt-2 text-3xl font-bold">Rp 0</p>
                        <p class="mt-1 text-sm text-slate-400">Cocok untuk coba & operasional sederhana</p>

                        <ul class="mt-6 space-y-3 text-sm text-slate-300">
                            <li class="flex gap-3">
                                <span class="text-emerald-300">✓</span>
                                POS kasir & riwayat transaksi</li>
                            <li class="flex gap-3">
                                <span class="text-emerald-300">✓</span>
                                Kelola produk & stok dasar</li>
                            <li class="flex gap-3">
                                <span class="text-emerald-300">✓</span>
                                Laporan ringkas</li>
                        </ul>

                        <a
                            href="/login"
                            class="mt-6 inline-flex w-full items-center justify-center rounded-xl bg-white/5 px-4 py-3 text-sm font-semibold text-white ring-1 ring-white/10 hover:bg-white/10">
                            Mulai
                        </a>
                    </div>

                    <!-- Pro -->
                    <div class="rounded-2xl bg-indigo-500/15 p-6 ring-1 ring-indigo-400/30">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-white">Premium</p>
                            <span
                                class="rounded-full bg-indigo-500/20 px-3 py-1 text-xs text-indigo-200 ring-1 ring-indigo-400/30">Paling populer</span>
                        </div>

                        <p class="mt-2 text-3xl font-bold">Rp 35.000<span class="text-base font-semibold text-slate-300">/bulan</span></p>
                        <p class="mt-1 text-sm text-slate-300">Untuk UMKM yang mulai ramai dan butuh kontrol</p>

                        <ul class="mt-6 space-y-3 text-sm text-slate-200">
                            <li class="flex gap-3">
                                <span class="text-emerald-300">✓</span>
                                Multi user + role (admin/kasir)</li>
                            <li class="flex gap-3">
                                <span class="text-emerald-300">✓</span>
                                Laporan lengkap & export</li>
                            <li class="flex gap-3">
                                <span class="text-emerald-300">✓</span>
                                Integrasi Midtrans (pembayaran online)</li>
                        </ul>

                        <a
                            href="/login"
                            class="mt-6 inline-flex w-full items-center justify-center rounded-xl bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400">
                            Upgrade Premium
                        </a>
                    </div>

                    <!-- Business -->
                    <div class="rounded-2xl bg-white/5 p-6 ring-1 ring-white/10">
                        <p class="text-sm font-semibold text-white">Business</p>
                        <p class="mt-2 text-3xl font-bold">Custom</p>
                        <p class="mt-1 text-sm text-slate-400">Untuk multi cabang / kebutuhan khusus</p>

                        <ul class="mt-6 space-y-3 text-sm text-slate-300">
                            <li class="flex gap-3">
                                <span class="text-emerald-300">✓</span>
                                Multi outlet/cabang</li>
                            <li class="flex gap-3">
                                <span class="text-emerald-300">✓</span>
                                Integrasi tambahan (API)</li>
                            <li class="flex gap-3">
                                <span class="text-emerald-300">✓</span>
                                SLA & support prioritas</li>
                        </ul>

                        <a
                            href="https://wa.me/6282131898358"
                            class="mt-6 inline-flex w-full items-center justify-center rounded-xl bg-white/5 px-4 py-3 text-sm font-semibold text-white ring-1 ring-white/10 hover:bg-white/10">
                            Konsultasi
                        </a>
                    </div>
                </div>
            </section>

            <!-- FAQ -->
            <section id="faq" class="mx-auto max-w-7xl px-6 pb-16 md:pb-24">
                <h2 class="text-3xl font-bold tracking-tight text-white">FAQ</h2>
                <div class="mt-8 grid gap-4 md:grid-cols-2">
                    <details class="rounded-2xl bg-white/5 p-6 ring-1 ring-white/10">
                        <summary class="cursor-pointer text-sm font-semibold text-white">iseller cocok buat usaha apa?</summary>
                        <p class="mt-3 text-sm text-slate-300">Cocok untuk UMKM seperti warung, café,
                            toko retail kecil, dan usaha jasa yang butuh pencatatan transaksi & laporan.</p>
                    </details>

                    <details class="rounded-2xl bg-white/5 p-6 ring-1 ring-white/10">
                        <summary class="cursor-pointer text-sm font-semibold text-white">Pembayaran Midtrans itu gimana?</summary>
                        <p class="mt-3 text-sm text-slate-300">Midtrans dipakai sebagai gateway
                            pembayaran. iseller mengirim order, lalu menerima notifikasi status pembayaran
                            lewat webhook.</p>
                    </details>

                    <details class="rounded-2xl bg-white/5 p-6 ring-1 ring-white/10">
                        <summary class="cursor-pointer text-sm font-semibold text-white">Apa bedanya Starter vs Premium?</summary>
                        <p class="mt-3 text-sm text-slate-300">Starter untuk kebutuhan dasar. Premium
                            menambah fitur seperti multi user/role, laporan lengkap, export, dan integrasi
                            pembayaran online.</p>
                    </details>

                    <details class="rounded-2xl bg-white/5 p-6 ring-1 ring-white/10">
                        <summary class="cursor-pointer text-sm font-semibold text-white">Bisa minta tampilan sesuai brand?</summary>
                        <p class="mt-3 text-sm text-slate-300">Bisa. Landing page ini tinggal ganti
                            warna, font, gambar, dan copywriting sesuai identitas iseller.</p>
                    </details>
                </div>
            </section>

            <!-- Login -->
            <section id="login" class="mx-auto max-w-7xl px-6 pb-16 md:pb-24">
                <div class="grid gap-8 md:grid-cols-2 md:items-center">
                    <div>
                        <h2 class="text-3xl font-bold tracking-tight text-white">Login ke iseller</h2>
                        <p class="mt-2 text-slate-300">
                            Masuk menggunakan email yang sudah terdaftar atau gunakan akun Google kamu.
                        </p>

                        <div class="mt-6 space-y-3 text-sm text-slate-300">
                            <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10">
                                <p class="font-semibold text-white">Tips</p>
                                <ul class="mt-2 list-disc space-y-1 pl-5">
                                    <li>Gunakan email & password yang sudah dibuat oleh admin toko.</li>
                                    <li>Jika lupa password, hubungi admin/owner untuk reset.</li>
                                    <li>Google Login cocok untuk akses cepat (tanpa ketik password).</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white/5 p-6 ring-1 ring-white/10">
                        <!-- FORM LOGIN EMAIL -->
                        <form action="/login" method="POST" class="space-y-4">
                            <!-- Laravel: kalau nanti dipakai di Blade, aktifkan @csrf -->
                            <!-- <input type="hidden" name="_token" value="{{ csrf_token() }}"> -->

                            <div>
                                <label for="email" class="mb-2 block text-sm font-semibold text-slate-200">Email</label>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    required="required"
                                    autocomplete="email"
                                    placeholder="contoh: kasir@tokokamu.com"
                                    class="w-full rounded-xl bg-slate-950/60 px-4 py-3 text-sm text-white placeholder:text-slate-400 ring-1 ring-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-400/60"/>
                                <p class="mt-1 text-xs text-slate-400">Format email harus valid, contoh: nama@domain.com</p>
                            </div>

                            <div>
                                <div class="flex items-center justify-between">
                                    <label for="password" class="mb-2 block text-sm font-semibold text-slate-200">Password</label>
                                    <a
                                        href="/forgot-password"
                                        class="text-xs text-indigo-200 hover:text-indigo-100">Lupa password?</a>
                                </div>
                                <input
                                    id="password"
                                    name="password"
                                    type="password"
                                    required="required"
                                    minlength="6"
                                    autocomplete="current-password"
                                    placeholder="Minimal 6 karakter"
                                    class="w-full rounded-xl bg-slate-950/60 px-4 py-3 text-sm text-white placeholder:text-slate-400 ring-1 ring-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-400/60"/>
                                <p class="mt-1 text-xs text-slate-400">Gunakan password yang sudah terdaftar.</p>
                            </div>

                            <div class="flex items-center justify-between">
                                <label class="inline-flex items-center gap-2 text-sm text-slate-300">
                                    <input
                                        type="checkbox"
                                        name="remember"
                                        class="h-4 w-4 rounded border-white/20 bg-slate-950/60"/>
                                    Ingat saya
                                </label>
                                <span class="text-xs text-slate-400">Akses aman untuk kasir & admin</span>
                            </div>

                            <button
                                type="submit"
                                class="w-full rounded-xl bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400">
                                Login
                            </button>

                            <!-- Divider -->
                            <div class="flex items-center gap-3 py-2">
                                <div class="h-px flex-1 bg-white/10"></div>
                                <span class="text-xs text-slate-400">atau</span>
                                <div class="h-px flex-1 bg-white/10"></div>
                            </div>

                            <!-- GOOGLE LOGIN BUTTON -->
                            <a
                                href="/auth/google"
                                class="inline-flex w-full items-center justify-center gap-3 rounded-xl bg-white/5 px-4 py-3 text-sm font-semibold text-white ring-1 ring-white/10 hover:bg-white/10">
                                <!-- Icon Google (SVG) -->
                                <svg width="18" height="18" viewbox="0 0 48 48" aria-hidden="true">
                                    <path
                                        fill="#FFC107"
                                        d="M43.611 20.083H42V20H24v8h11.303C33.468 32.91 29.197 36 24 36c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/>
                                    <path
                                        fill="#FF3D00"
                                        d="M6.306 14.691l6.571 4.819C14.655 16.108 19.01 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4c-7.682 0-14.354 4.336-17.694 10.691z"/>
                                    <path
                                        fill="#4CAF50"
                                        d="M24 44c5.166 0 9.86-1.977 13.409-5.197l-6.19-5.238C29.174 35.091 26.715 36 24 36c-5.176 0-9.436-3.069-11.292-7.463l-6.522 5.024C9.479 39.556 16.227 44 24 44z"/>
                                    <path
                                        fill="#1976D2"
                                        d="M43.611 20.083H42V20H24v8h11.303a12.05 12.05 0 0 1-4.084 5.565l.003-.002 6.19 5.238C36.971 40.205 44 36 44 24c0-1.341-.138-2.65-.389-3.917z"/>
                                </svg>
                                Login dengan Google
                            </a>

                            <p class="pt-2 text-center text-xs text-slate-400">
                                Belum punya akun?
                                <span class="text-slate-300">Hubungi admin/owner</span>
                                untuk dibuatkan akses.
                            </p>
                        </form>
                    </div>
                </div>
            </section>

            <!-- CTA -->
            <section id="cta" class="mx-auto max-w-7xl px-6 pb-20">
                <div
                    class="rounded-2xl bg-gradient-to-br from-indigo-500/20 to-cyan-500/10 p-8 ring-1 ring-white/10 md:p-10">
                    <div class="grid gap-8 md:grid-cols-2 md:items-center">
                        <div>
                            <h3 class="text-2xl font-bold text-white">Siap bikin kasir UMKM kamu lebih rapi?</h3>
                            <p class="mt-2 text-slate-200/90">
                                Mulai dari Starter dulu, lalu upgrade ke Premium saat kamu butuh fitur lebih
                                lengkap.
                            </p>
                        </div>

                        <form class="grid gap-3 sm:grid-cols-3">
                            <input
                                type="text"
                                placeholder="Nama usaha"
                                class="w-full rounded-xl bg-slate-950/60 px-4 py-3 text-sm text-white placeholder:text-slate-400 ring-1 ring-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-400/60"/>
                            <input
                                type="email"
                                placeholder="Email"
                                class="w-full rounded-xl bg-slate-950/60 px-4 py-3 text-sm text-white placeholder:text-slate-400 ring-1 ring-white/10 focus:outline-none focus:ring-2 focus:ring-indigo-400/60"/>
                            <button
                                type="button"
                                class="rounded-xl bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400">
                                Minta Demo
                            </button>
                            <p class="sm:col-span-3 text-xs text-slate-300">
                                *Form ini contoh UI saja. Nanti bisa kamu sambungkan ke backend / email /
                                WhatsApp.
                            </p>
                        </form>
                    </div>
                </div>
            </section>

            <!-- Footer -->
            <footer class="border-t border-white/10">
                <div
                    class="mx-auto flex max-w-7xl flex-col gap-4 px-6 py-10 md:flex-row md:items-center md:justify-between">
                    <p class="text-sm text-slate-400">©
                        <span id="year"></span>
                        iseller. All rights reserved.</p>
                    <div class="flex flex-wrap gap-6 text-sm">
                        <a href="#fitur" class="text-slate-300 hover:text-white">Fitur</a>
                        <a href="#midtrans" class="text-slate-300 hover:text-white">Midtrans</a>
                        <a href="#premium" class="text-slate-300 hover:text-white">Premium</a>
                        <a href="#" class="text-slate-300 hover:text-white">Kontak</a>
                    </div>
                </div>
            </footer>
        </main>

        <script>
            document
                .getElementById("year")
                .textContent = new Date().getFullYear();
        </script>
    </body>
</html>
