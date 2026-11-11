<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Kamar - Kos-Ku</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gray-50 font-sans text-gray-800">
    <header class="bg-blue-800 px-4 py-8 text-white shadow-lg sm:px-8">
        <div class="mx-auto max-w-5xl">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <a href="{{ route('home') }}" class="mb-5 inline-flex items-center gap-2 text-sm text-blue-100 hover:text-white">
                        <i class="fa-solid fa-arrow-left"></i>
                        Kembali ke Beranda
                    </a>
                    <p class="text-sm font-bold uppercase tracking-widest text-blue-200">Kos-Ku Smart Living</p>
                    <h1 class="mt-2 text-3xl font-extrabold sm:text-4xl">Pesan Kamar Kos</h1>
                    <p class="mt-2 max-w-2xl text-sm text-blue-100 sm:text-base">
                        Pilih kamar yang tersedia, tentukan tanggal masuk dan durasi sewa.
                    </p>
                </div>
                <div class="flex gap-2 text-sm">
                    <a href="{{ route('rooms.index') }}" class="rounded-full bg-white/10 px-4 py-2 hover:bg-white/20">Katalog</a>
                    <a href="{{ route('orders.index') }}" class="rounded-full bg-white/10 px-4 py-2 hover:bg-white/20">Pesanan</a>
                </div>
            </div>
        </div>
    </header>

    <main class="mx-auto grid max-w-5xl gap-6 px-4 py-8 md:grid-cols-[1.2fr_0.8fr] sm:px-8">
        <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-7">
            <h2 class="mb-5 text-xl font-bold">Form Pemesanan</h2>

            @if (session('status'))
                <div role="status" class="mb-5 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                    {{ session('status') }}
                    @if (session('booking_code'))
                        <p class="mt-1 font-bold">Kode pesanan: {{ session('booking_code') }}</p>
                    @endif
                </div>
            @endif

            @if ($errors->any())
                <div role="alert" class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($kamars->isEmpty())
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-900">
                    <p class="font-bold">Belum ada kamar tersedia.</p>
                    <p class="mt-1">Silakan hubungi pengelola atau coba kembali setelah data kamar ditambahkan.</p>
                </div>
            @else
                <form method="POST" action="{{ route('booking.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="kamar_id" class="mb-1.5 block text-sm font-semibold text-gray-700">Pilih kamar</label>
                        <select id="kamar_id" name="kamar_id" required
                                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                            <option value="">-- Pilih kamar tersedia --</option>
                            @foreach ($kamars as $kamar)
                                <option value="{{ $kamar->id }}" @selected(old('kamar_id', $selectedKamarId ?? null) == $kamar->id)>
                                    {{ $kamar->tipe_kamar }} - No. {{ $kamar->nomor_kamar }}
                                    (Rp {{ number_format((float) $kamar->harga, 0, ',', '.') }}/bulan)
                                </option>
                            @endforeach
                        </select>
                        @error('kamar_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tanggal_masuk" class="mb-1.5 block text-sm font-semibold text-gray-700">Tanggal masuk</label>
                        <input id="tanggal_masuk" name="tanggal_masuk" type="date"
                               min="{{ now()->toDateString() }}" value="{{ old('tanggal_masuk') }}" required
                               class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                        @error('tanggal_masuk')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="durasi_bulan" class="mb-1.5 block text-sm font-semibold text-gray-700">Durasi sewa</label>
                        <select id="durasi_bulan" name="durasi_bulan" required
                                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                            <option value="1" @selected(old('durasi_bulan', '1') == '1')>1 bulan</option>
                            <option value="3" @selected(old('durasi_bulan') == '3')>3 bulan (hemat 5%)</option>
                            <option value="6" @selected(old('durasi_bulan') == '6')>6 bulan (hemat 10%)</option>
                        </select>
                    </div>

                    <button type="submit"
                            class="w-full rounded-xl bg-blue-600 px-5 py-3 font-bold text-white shadow-lg shadow-blue-100 transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Lanjutkan Pemesanan
                    </button>
                </form>
            @endif
        </section>

        <aside class="h-fit rounded-2xl border border-blue-100 bg-blue-50 p-5 sm:p-7">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-white text-xl text-blue-700 shadow-sm">
                <i class="fa-solid fa-circle-info"></i>
            </div>
            <h2 class="text-lg font-bold text-blue-950">Informasi pemesanan</h2>
            <ul class="mt-4 space-y-3 text-sm leading-6 text-blue-900">
                <li class="flex gap-2"><i class="fa-solid fa-check mt-1 text-blue-600"></i><span>Harga dihitung dari tarif kamar dan durasi sewa.</span></li>
                <li class="flex gap-2"><i class="fa-solid fa-check mt-1 text-blue-600"></i><span>Sewa 3 bulan mendapat diskon 5%; 6 bulan mendapat diskon 10%.</span></li>
                <li class="flex gap-2"><i class="fa-solid fa-clock mt-1 text-blue-600"></i><span>Pesanan disimpan dengan status menunggu pembayaran.</span></li>
            </ul>
            <p class="mt-5 border-t border-blue-200 pt-4 text-xs leading-5 text-blue-800">
                Tahap ini membuat pesanan. Pembayaran QRIS akan disambungkan pada tahap berikutnya.
            </p>
        </aside>
    </main>
</body>
</html>
