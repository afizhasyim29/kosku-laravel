@extends('layouts.kosku')

@section('title', 'Invoice '.$pesanan->kode_tiket.' - Kos-Ku')

@section('content')
    <section class="mx-auto max-w-3xl rounded-3xl bg-white p-6 shadow-sm sm:p-10">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div><p class="text-sm font-bold uppercase tracking-widest text-blue-700">Kos-Ku · Invoice</p><h1 class="mt-2 text-3xl font-extrabold">{{ $pesanan->kode_tiket }}</h1></div>
            <span class="rounded-full bg-{{ $pesanan->status_pembayaran === 'lunas' ? 'emerald' : 'amber' }}-100 px-4 py-2 text-sm font-bold text-{{ $pesanan->status_pembayaran === 'lunas' ? 'emerald' : 'amber' }}-800">{{ str_replace('_', ' ', ucfirst($pesanan->status_pembayaran)) }}</span>
        </div>
        <dl class="mt-8 grid gap-4 border-y py-6 sm:grid-cols-2">
            <div><dt class="text-sm text-slate-500">Nama</dt><dd class="mt-1 font-semibold">{{ $pesanan->user->nama }}</dd></div>
            <div><dt class="text-sm text-slate-500">Kamar</dt><dd class="mt-1 font-semibold">{{ $pesanan->kamar->nomor_kamar }} · {{ $pesanan->kamar->tipe_kamar }}</dd></div>
            <div><dt class="text-sm text-slate-500">Tanggal masuk</dt><dd class="mt-1 font-semibold">{{ $pesanan->tanggal_masuk->format('d M Y') }}</dd></div>
            <div><dt class="text-sm text-slate-500">Durasi</dt><dd class="mt-1 font-semibold">{{ $pesanan->durasi_bulan }} bulan</dd></div>
        </dl>
        <div class="mt-6 flex items-center justify-between text-lg"><span>Total</span><strong>Rp {{ number_format((float) $pesanan->total_bayar, 0, ',', '.') }}</strong></div>
        @if ($pesanan->status_pembayaran === 'lunas')
            <div class="mt-7 text-center">
                <canvas class="mx-auto hidden" data-qrcode="{{ $pesanan->kode_tiket }}" data-qrcode-width="180" aria-label="QR tiket penghuni"></canvas>
                <div data-qrcode-status class="mx-auto flex h-[180px] w-[180px] flex-col items-center justify-center bg-slate-50 p-4 text-slate-700">
                    <span class="font-semibold">Menyiapkan QR tiket...</span>
                    <span class="mt-2 break-all font-mono text-xs">{{ $pesanan->kode_tiket }}</span>
                </div>
                <p class="mt-2 text-xs text-slate-500">Tiket untuk validasi penghuni.</p>
            </div>
        @else
            <p class="mt-6 rounded-xl bg-amber-50 p-4 text-sm text-amber-800">Pembayaran belum diverifikasi. Tiket penghuni belum aktif.</p>
        @endif
        <a href="{{ route('orders.index') }}" class="mt-7 inline-flex rounded-xl border px-5 py-3 font-semibold">Kembali ke Pesanan</a>
    </section>
@endsection
