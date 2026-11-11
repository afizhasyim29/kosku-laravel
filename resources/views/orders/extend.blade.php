@extends('layouts.kosku')

@section('title', 'Perpanjang Sewa - Kos-Ku')

@section('content')
    <section class="mx-auto max-w-xl rounded-3xl bg-white p-6 shadow-sm sm:p-9">
        <p class="text-sm font-bold uppercase tracking-widest text-blue-700">Kamar {{ $pesanan->kamar->nomor_kamar }}</p>
        <h1 class="mt-2 text-3xl font-extrabold">Perpanjang Masa Sewa</h1>
        <p class="mt-3 text-sm text-slate-600">Tarif saat ini Rp {{ number_format((float) $pesanan->kamar->harga, 0, ',', '.') }} per bulan. Total perpanjangan dihitung di server.</p>
        <form method="POST" action="{{ route('extensions.store', $pesanan) }}" class="mt-6 space-y-4">
            @csrf
            <label for="durasi_bulan" class="block text-sm font-semibold">Tambahan durasi</label>
            <select id="durasi_bulan" name="durasi_bulan" required class="w-full rounded-xl border-slate-300">
                <option value="1">1 bulan</option>
                <option value="3">3 bulan</option>
                <option value="6">6 bulan</option>
            </select>
            <button class="w-full rounded-xl bg-blue-700 px-5 py-3 font-bold text-white hover:bg-blue-800">Lanjutkan ke Pembayaran</button>
        </form>
    </section>
@endsection
