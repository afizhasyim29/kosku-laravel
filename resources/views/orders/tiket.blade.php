@extends('layouts.kosku')

@section('title', 'Tiket Penghuni '.$pesanan->kode_tiket.' - Kos-Ku')

@section('content')
    @php($tanggalAkhir = $pesanan->tanggal_masuk->copy()->addMonths($pesanan->durasi_bulan))
    <section class="mx-auto max-w-2xl overflow-hidden rounded-3xl bg-white shadow-lg">
        <div class="bg-gradient-to-r from-blue-950 to-blue-700 p-7 text-white sm:p-9">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-200">Kos-Ku Smart Living</p>
            <h1 class="mt-2 text-3xl font-extrabold">Tiket Penghuni</h1>
            <p class="mt-2 text-sm text-blue-100">Pembayaran berhasil · Pesanan {{ $pesanan->kode_tiket }}</p>
        </div>
        <div class="grid gap-6 p-6 sm:grid-cols-[1fr_auto] sm:p-9">
            <dl class="space-y-4">
                <div><dt class="text-xs uppercase tracking-wider text-slate-400">Nama penghuni</dt><dd class="mt-1 text-lg font-bold">{{ $pesanan->user->nama }}</dd></div>
                <div><dt class="text-xs uppercase tracking-wider text-slate-400">Kamar</dt><dd class="mt-1 font-semibold">{{ $pesanan->kamar->nomor_kamar }} · {{ $pesanan->kamar->tipe_kamar }}</dd></div>
                <div><dt class="text-xs uppercase tracking-wider text-slate-400">Masa sewa</dt><dd class="mt-1 font-semibold">{{ $pesanan->tanggal_masuk->format('d M Y') }} – {{ $tanggalAkhir->format('d M Y') }}</dd></div>
                <div><dt class="text-xs uppercase tracking-wider text-slate-400">Durasi</dt><dd class="mt-1 font-semibold">{{ $pesanan->durasi_bulan }} bulan</dd></div>
                <div><dt class="text-xs uppercase tracking-wider text-slate-400">Kode tiket</dt><dd class="mt-1 break-all font-mono font-bold text-blue-800">{{ $pesanan->kode_tiket }}</dd></div>
            </dl>
            <div class="mx-auto h-fit rounded-2xl border border-slate-200 p-3 text-center">
                <canvas class="hidden" data-qrcode="{{ $pesanan->kode_tiket }}" data-qrcode-width="170" aria-label="QR tiket penghuni"></canvas>
                <div data-qrcode-status class="flex h-[170px] w-[170px] flex-col items-center justify-center bg-slate-50 p-4 text-slate-600">
                    <span class="font-semibold">Menyiapkan QR tiket...</span>
                    <span class="mt-2 break-all font-mono text-xs">{{ $pesanan->kode_tiket }}</span>
                </div>
                <p class="mt-2 text-xs text-slate-500">Tunjukkan QR saat masuk</p>
            </div>
        </div>
        <div class="border-t border-dashed px-6 py-5 sm:px-9">
            <p class="text-xs leading-5 text-slate-500">Tiket aktif untuk pesanan yang telah dibayar. QR ini merupakan kode validasi untuk pengelola dan tidak membuka kunci fisik.</p>
            <div class="mt-4 flex flex-wrap gap-2 print:hidden">
                <a href="{{ route('orders.index') }}" class="rounded-xl border px-4 py-2 text-sm font-semibold">Kembali ke Pesanan</a>
                <a href="{{ route('orders.receipt', $pesanan->kode_tiket) }}" class="rounded-xl border px-4 py-2 text-sm font-semibold">Lihat Kuitansi</a>
                <a href="{{ route('resident.card') }}" class="rounded-xl border px-4 py-2 text-sm font-semibold">Kartu Penghuni</a>
                <button onclick="window.print()" class="rounded-xl bg-blue-700 px-4 py-2 text-sm font-bold text-white">Cetak Tiket</button>
            </div>
        </div>
    </section>
@endsection
