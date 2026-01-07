<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>Registrasi — iseller</title>
        <meta
            name="description"
            content="Halaman registrasi iseller menggunakan Tailwind CSS (Laravel + Filament)."/>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>

    <body class="min-h-screen bg-slate-950 text-slate-100">
        <!-- Background glow -->
        <div class="pointer-events-none fixed inset-0 overflow-hidden">
            <div
                class="absolute -top-40 left-1/2 h-[520px] w-[520px] -translate-x-1/2 rounded-full bg-indigo-500/20 blur-3xl"></div>
            <div
                class="absolute top-40 -left-40 h-[520px] w-[520px] rounded-full bg-cyan-500/10 blur-3xl"></div>
        </div>

        <main
            class="relative z-10 mx-auto flex max-w-7xl items-center justify-center px-6 py-14">
            <div class="w-full max-w-md rounded-2xl bg-white/5 p-6 ring-1 ring-white/10">
                <!-- Brand -->
                <a href="/admin/login" class="mb-6 inline-flex items-center gap-2">
                    <span
                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-white/10 ring-1 ring-white/10">
                        <span class="text-lg font-bold text-white">i</span>
                    </span>
                    <span class="text-lg font-semibold tracking-tight">iseller</span>
                </a>

                <h1 class="text-2xl font-bold tracking-tight text-white">Buat Akun</h1>
                <p class="mt-1 text-sm text-slate-300">Daftar untuk mulai menggunakan iseller.</p>

                <!-- ALERT: error dari Laravel (validasi) -->
                @if ($errors->any())
                <div
                    class="mt-4 rounded-xl bg-red-500/10 p-4 text-sm text-red-100 ring-1 ring-red-400/20">
                    <div class="mb-2 font-semibold">Registrasi gagal:</div>
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Form Register (Laravel backend) -->
                <form
                    id="registerForm"
                    class="mt-6 space-y-4"
                    method="POST"
                    action="{{ route('register.store') }}">
                    @csrf

                    <div>
                        <label for="name" class="mb-2 block text-sm font-semibold text-slate-200">Nama Lengkap</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            required="required"
                            value="{{ old('name') }}"
                            placeholder="Nama pemilik / kasir"
                            class="w-full rounded-xl bg-slate-950/60 px-4 py-3 text-sm text-white placeholder:text-slate-400 ring-1 ring-white/10 outline-none focus:ring-2 focus:ring-indigo-400/60"/>
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold text-slate-200">Email</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            required="required"
                            autocomplete="email"
                            value="{{ old('email') }}"
                            placeholder="contoh@tokokamu.com"
                            class="w-full rounded-xl bg-slate-950/60 px-4 py-3 text-sm text-white placeholder:text-slate-400 ring-1 ring-white/10 outline-none focus:ring-2 focus:ring-indigo-400/60"/>
                        <p class="mt-1 text-xs text-slate-400">Pastikan email aktif untuk login.</p>
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-semibold text-slate-200">Password</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            minlength="6"
                            required="required"
                            autocomplete="new-password"
                            placeholder="Minimal 6 karakter"
                            class="w-full rounded-xl bg-slate-950/60 px-4 py-3 text-sm text-white placeholder:text-slate-400 ring-1 ring-white/10 outline-none focus:ring-2 focus:ring-indigo-400/60"/>
                        <p class="mt-1 text-xs text-slate-400">Minimal 6 karakter.</p>
                    </div>

                    <div>
                        <label for="password2" class="mb-2 block text-sm font-semibold text-slate-200">Konfirmasi Password</label>
                        <input
                            id="password2"
                            name="password_confirmation"
                            type="password"
                            minlength="6"
                            required="required"
                            autocomplete="new-password"
                            placeholder="Ulangi password"
                            class="w-full rounded-xl bg-slate-950/60 px-4 py-3 text-sm text-white placeholder:text-slate-400 ring-1 ring-white/10 outline-none focus:ring-2 focus:ring-indigo-400/60"/>
                        <p id="matchHint" class="mt-1 hidden text-xs text-red-200">Password tidak sama.</p>
                    </div>

                    <label class="inline-flex items-start gap-2 text-sm text-slate-300">
                        <input
                            id="agree"
                            name="agree"
                            type="checkbox"
                            required="required"
                            class="mt-1 h-4 w-4 rounded border-white/20 bg-slate-950/60"/>
                        <span>
                            Saya setuju dengan
                            <a href="#" class="text-indigo-200 hover:text-indigo-100">Syarat & Ketentuan</a>
                            dan
                            <a href="#" class="text-indigo-200 hover:text-indigo-100">Kebijakan Privasi</a>.
                        </span>
                    </label>

                    <button
                        type="submit"
                        class="w-full rounded-xl bg-indigo-500 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-400">
                        Daftar
                    </button>

                    <div class="flex items-center gap-3 py-2">
                        <div class="h-px flex-1 bg-white/10"></div>
                        <span class="text-xs text-slate-400">atau</span>
                        <div class="h-px flex-1 bg-white/10"></div>
                    </div>

                    <!-- Google Register (UI only) -->
                    <a
                        href="{{ route('auth.google.redirect') }}"
                        class="inline-flex w-full items-center justify-center gap-3 rounded-xl bg-white/5 px-4 py-3 text-sm font-semibold text-white ring-1 ring-white/10 hover:bg-white/10">
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
                        Daftar dengan Google
                    </a>

                    <p class="pt-2 text-center text-xs text-slate-400">
                        Sudah punya akun?
                        <a
                            href="/admin/login"
                            class="font-semibold text-indigo-300 hover:text-indigo-200">Login</a>
                    </p>
                </form>

                <p class="mt-6 text-center text-xs text-slate-400">©
                    <span id="year"></span>
                    iseller</p>
            </div>
        </main>

        <script>
            // Year
            document
                .getElementById("year")
                .textContent = new Date().getFullYear();

            // Google demo
            function showGoogleDemo() {
                alert("Daftar dengan Google (Demo) — nanti sambungkan ke Google OAuth.");
            }

            // Optional UX: validasi password sama sebelum submit
            document
                .getElementById("registerForm")
                .addEventListener("submit", function (e) {
                    const p1 = document
                        .getElementById("password")
                        .value
                        .trim();
                    const p2 = document
                        .getElementById("password2")
                        .value
                        .trim();
                    const hint = document.getElementById("matchHint");

                    if (p1 !== p2) {
                        e.preventDefault();
                        hint
                            .classList
                            .remove("hidden");
                        document
                            .getElementById("password2")
                            .focus();
                        return;
                    }

                    hint
                        .classList
                        .add("hidden");
                });
        </script>
    </body>
</html>
