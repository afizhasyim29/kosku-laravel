@extends('layouts.kosku')

@section('title', 'Kartu Penghuni - Kos-Ku')

@section('content')
    <section class="mx-auto max-w-xl rounded-3xl bg-gradient-to-br from-blue-950 to-blue-700 p-7 text-white shadow-xl sm:p-10">
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-200">Kos-Ku Smart Living</p>
        <h1 class="mt-2 text-2xl font-extrabold">Kartu Penghuni</h1>
        @if ($pesanan)
            <div class="mt-7 grid gap-6 sm:grid-cols-[1fr_auto] sm:items-center">
                <div class="space-y-3 text-sm">
                    <p class="text-blue-200">Nama penghuni</p><p class="text-lg font-bold">{{ auth()->user()->nama }}</p>
                    <p class="pt-2 text-blue-200">Kamar</p><p class="text-lg font-bold">{{ $pesanan->kamar->nomor_kamar }} · {{ $pesanan->kamar->tipe_kamar }}</p>
                    <p class="pt-2 text-blue-200">Kode tiket</p><p class="font-mono font-bold">{{ $pesanan->kode_tiket }}</p>
                </div>
                <div class="rounded-2xl bg-white p-3 text-center">
                    <canvas class="hidden" data-qrcode="{{ $pesanan->kode_tiket }}" data-qrcode-width="150" aria-label="QR kartu penghuni"></canvas>
                    <div data-qrcode-status class="flex h-[150px] w-[150px] flex-col items-center justify-center bg-slate-50 p-3 text-slate-700">
                        <span class="font-semibold">Menyiapkan QR...</span>
                        <span class="mt-2 break-all font-mono text-xs">{{ $pesanan->kode_tiket }}</span>
                    </div>
                </div>
            </div>
        @else
            <p class="mt-6 rounded-xl bg-white/10 p-4">Belum ada pesanan lunas pada akun ini.</p>
        @endif
    </section>
@endsection
