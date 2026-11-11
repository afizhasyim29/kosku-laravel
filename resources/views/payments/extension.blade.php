@extends('layouts.kosku')

@section('title', 'Pembayaran Perpanjangan - Kos-Ku')

@section('content')
    <section class="mx-auto max-w-3xl rounded-3xl bg-white p-6 shadow-sm sm:p-9">
        <p class="text-sm font-bold uppercase tracking-widest text-blue-700">Perpanjangan sewa</p>
        <h1 class="mt-2 text-3xl font-extrabold">Pembayaran QRIS</h1>
        <div class="mt-6 grid gap-6 md:grid-cols-2">
            <div class="space-y-3 rounded-2xl bg-slate-50 p-5 text-sm">
                <p>Kode: <strong>{{ $perpanjangan->kode_tiket }}</strong></p>
                <p>Kamar: <strong>{{ $perpanjangan->pesanan->kamar->nomor_kamar }}</strong></p>
                <p>Tambahan sewa: <strong>{{ $perpanjangan->durasi_bulan }} bulan</strong></p>
                <p>Total: <strong class="text-lg text-blue-800">Rp {{ number_format((float) $perpanjangan->total_bayar, 0, ',', '.') }}</strong></p>
            </div>
            <div class="text-center">
                <img src="{{ asset('images/qris.jpg') }}" alt="QRIS pembayaran Kos-Ku" class="mx-auto max-h-64 rounded-xl border p-2">
                <p class="mt-3 text-xs text-slate-500">Cantumkan kode perpanjangan sebagai referensi pembayaran.</p>
            </div>
        </div>
        @if ($perpanjangan->status_pembayaran === 'menunggu_pembayaran')
            <form method="POST" action="{{ route('extensions.payment.confirm', $perpanjangan) }}" class="mt-6">
                @csrf
                <button class="w-full rounded-xl bg-emerald-700 px-5 py-3 font-bold text-white hover:bg-emerald-800">Simulasikan Pembayaran Perpanjangan Berhasil</button>
            </form>
            <p class="mt-3 text-center text-xs text-slate-500">Mode demo: tombol ini langsung melunasi perpanjangan dan menambah masa sewa.</p>
        @else
            <p class="mt-5 rounded-xl bg-amber-50 p-4 text-sm text-amber-800">Perpanjangan ini berstatus {{ str_replace('_', ' ', $perpanjangan->status_pembayaran) }}.</p>
        @endif
    </section>
@endsection
