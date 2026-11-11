@extends('layouts.kosku')

@section('title', 'Kuitansi '.$pesanan->kode_tiket.' - Kos-Ku')

@section('content')
    <section class="mx-auto max-w-2xl rounded-3xl border-2 border-dashed border-blue-200 bg-white p-8 shadow-sm sm:p-10">
        <div class="text-center">
            <i class="fa-solid fa-circle-check text-5xl text-emerald-600"></i>
            <p class="mt-4 text-sm font-bold uppercase tracking-widest text-blue-700">Bukti pembayaran</p>
            <h1 class="mt-2 text-3xl font-extrabold">Kuitansi Lunas</h1>
        </div>
        <dl class="mt-8 space-y-4 border-y py-6">
            <div class="flex justify-between gap-4"><dt class="text-slate-500">Kode</dt><dd class="font-semibold">{{ $pesanan->kode_tiket }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-slate-500">Penghuni</dt><dd class="font-semibold">{{ $pesanan->user->nama }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-slate-500">Kamar</dt><dd class="font-semibold">{{ $pesanan->kamar->nomor_kamar }} · {{ $pesanan->kamar->tipe_kamar }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-slate-500">Tanggal masuk</dt><dd class="font-semibold">{{ $pesanan->tanggal_masuk->format('d M Y') }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-slate-500">Durasi</dt><dd class="font-semibold">{{ $pesanan->durasi_bulan }} bulan</dd></div>
        </dl>
        <div class="mt-5 flex justify-between text-lg"><span>Total diterima</span><strong>Rp {{ number_format((float) $pesanan->total_bayar, 0, ',', '.') }}</strong></div>
        <p class="mt-2 text-right text-xs text-slate-500">Diverifikasi pada {{ $pesanan->paid_at?->format('d M Y H:i') ?? '—' }}</p>
        <button onclick="window.print()" class="mt-7 rounded-xl bg-blue-700 px-5 py-3 font-bold text-white print:hidden">Cetak Kuitansi</button>
    </section>
@endsection
