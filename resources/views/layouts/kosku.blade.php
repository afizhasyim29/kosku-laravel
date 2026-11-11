<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kos-Ku')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite('resources/js/app.js')
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800">
    <nav class="bg-blue-950 text-white shadow">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-3 px-4 py-4">
            <a href="{{ route('home') }}" class="font-extrabold tracking-wide">Kos-Ku <span class="text-xs font-normal text-blue-200">Smart Living</span></a>
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm">
                <a href="{{ route('rooms.index') }}" class="hover:text-blue-200">Katalog</a>
                <a href="{{ route('orders.index') }}" class="hover:text-blue-200">Pesanan</a>
                <a href="{{ route('fasilitas') }}" class="hover:text-blue-200">Fasilitas</a>
                <a href="{{ route('layanan') }}" class="hover:text-blue-200">Layanan</a>
                <a href="{{ route('bantuan') }}" class="hover:text-blue-200">Bantuan</a>
                <a href="{{ route('aturan') }}" class="hover:text-blue-200">Aturan</a>
                <a href="{{ route('faq') }}" class="hover:text-blue-200">FAQ</a>
                <a href="{{ route('scanner.index') }}" class="font-semibold text-amber-300 hover:text-amber-200">Scanner</a>
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.index') }}" class="font-semibold text-amber-300 hover:text-amber-200">Pengelola</a>
                @endif
                <span class="text-blue-200">{{ auth()->user()->nama }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="rounded-full bg-white/10 px-3 py-1 hover:bg-white/20">Keluar</button>
                </form>
            </div>
        </div>
    </nav>

    @if (session('status'))
        <div class="mx-auto mt-5 max-w-7xl px-4">
            <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('status') }}</div>
        </div>
    @endif
    @if ($errors->any())
        <div class="mx-auto mt-5 max-w-7xl px-4">
            <div role="alert" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <ul class="list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        </div>
    @endif

    <main class="mx-auto max-w-7xl px-4 py-8">
        @yield('content')
    </main>
    <footer class="border-t bg-white py-6 text-center text-xs text-slate-500">&copy; {{ now()->year }} Kos-Ku Smart Living</footer>
</body>
</html>
