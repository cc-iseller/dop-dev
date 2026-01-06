<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <title>Setup Toko — iSeller</title>
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
            class="relative z-10 mx-auto flex min-h-screen max-w-5xl items-center justify-center px-6 py-14">
            <div
                class="w-full max-w-xl rounded-3xl border border-white/10 bg-white/5 p-8 shadow-2xl backdrop-blur">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold tracking-tight">Buat Toko Pertama</h1>
                    <p class="mt-2 text-sm text-slate-300">
                        Sebelum masuk ke dashboard, kamu perlu membuat 1 toko dulu.
                    </p>
                </div>

                @if ($errors->any())
                <div
                    class="mb-5 rounded-xl border border-red-500/20 bg-red-500/10 p-4 text-sm text-red-200">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('setup.store.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-200">
                            Nama Toko
                        </label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Contoh: Toko Ardyn Mart"
                            class="w-full rounded-xl border border-white/10 bg-slate-900/60 px-4 py-3 text-slate-100 outline-none ring-0 placeholder:text-slate-500 focus:border-indigo-400/60 focus:ring-4 focus:ring-indigo-500/10"
                            required="required"/>
                        <p class="mt-2 text-xs text-slate-400">
                            Nama ini akan tampil di panel admin dan struk transaksi.
                        </p>
                    </div>

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-500 px-4 py-3 font-semibold text-white shadow-lg shadow-indigo-500/20 transition hover:bg-indigo-400 active:scale-[0.99]">
                        Buat Toko & Masuk Dashboard
                    </button>

                    <div class="pt-2 text-center text-xs text-slate-400">
                        Login sebagai:
                        <span class="text-slate-200">{{ auth()->user()->email }}</span>
                    </div>
                </form>
            </div>
        </main>
    </body>
</html>
