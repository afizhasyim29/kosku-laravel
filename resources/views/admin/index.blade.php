@extends('layouts.kosku')

@section('title', 'Pengelola - Kos-Ku')

@section('content')
    <p class="text-sm font-bold uppercase tracking-widest text-blue-700">Panel pengelola</p>
    <h1 class="mt-2 text-3xl font-extrabold">Verifikasi Pembayaran &amp; Laporan</h1>
    <a href="{{ route('scanner.index') }}" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-blue-700 px-5 py-3 font-bold text-white hover:bg-blue-800">
        <i class="fa-solid fa-qrcode" aria-hidden="true"></i> Buka Scanner Tiket Penghuni
    </a>

    <section class="mt-7 rounded-2xl bg-white p-5 shadow-sm">
        <h2 class="text-xl font-bold">Konfirmasi Pembayaran Booking</h2>
        <div class="mt-4 space-y-3">
            @forelse ($pesanans as $pesanan)
                <article class="flex flex-wrap items-center justify-between gap-4 rounded-xl border p-4">
                    <div><p class="font-bold">{{ $pesanan->kode_tiket }} · {{ $pesanan->user->nama }}</p><p class="text-sm text-slate-600">Kamar {{ $pesanan->kamar->nomor_kamar }} · Rp {{ number_format((float) $pesanan->total_bayar, 0, ',', '.') }}</p></div>
                    <div class="flex gap-2">
                        @foreach (['lunas' => 'Verifikasi Lunas', 'ditolak' => 'Tolak'] as $status => $label)
                            <form method="POST" action="{{ route('admin.booking-payment', $pesanan) }}">@csrf<input type="hidden" name="status" value="{{ $status }}"><button class="rounded-lg px-3 py-2 text-sm font-semibold {{ $status === 'lunas' ? 'bg-emerald-700 text-white' : 'bg-red-50 text-red-700' }}">{{ $label }}</button></form>
                        @endforeach
                    </div>
                </article>
            @empty
                <p class="text-sm text-slate-500">Tidak ada booking menunggu verifikasi.</p>
            @endforelse
        </div>
    </section>

    <section class="mt-6 rounded-2xl bg-white p-5 shadow-sm">
        <h2 class="text-xl font-bold">Konfirmasi Perpanjangan</h2>
        <div class="mt-4 space-y-3">
            @forelse ($perpanjangans as $extension)
                <article class="flex flex-wrap items-center justify-between gap-4 rounded-xl border p-4">
                    <div><p class="font-bold">{{ $extension->kode_tiket }} · {{ $extension->user->nama }}</p><p class="text-sm text-slate-600">Kamar {{ $extension->pesanan->kamar->nomor_kamar }} · {{ $extension->durasi_bulan }} bulan · Rp {{ number_format((float) $extension->total_bayar, 0, ',', '.') }}</p></div>
                    <div class="flex gap-2">
                        @foreach (['lunas' => 'Verifikasi Lunas', 'ditolak' => 'Tolak'] as $status => $label)
                            <form method="POST" action="{{ route('admin.extension-payment', $extension) }}">@csrf<input type="hidden" name="status" value="{{ $status }}"><button class="rounded-lg px-3 py-2 text-sm font-semibold {{ $status === 'lunas' ? 'bg-emerald-700 text-white' : 'bg-red-50 text-red-700' }}">{{ $label }}</button></form>
                        @endforeach
                    </div>
                </article>
            @empty
                <p class="text-sm text-slate-500">Tidak ada perpanjangan menunggu verifikasi.</p>
            @endforelse
        </div>
    </section>

    <section class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <h2 class="text-xl font-bold">Laporan Kendala</h2>
            <div class="mt-4 space-y-3">
                @forelse ($laporans as $laporan)
                    <article class="rounded-xl border p-4">
                        <div class="flex justify-between gap-3"><strong>{{ $laporan->user->nama }} · Kamar {{ $laporan->kamar->nomor_kamar }}</strong><span class="text-xs text-slate-500">{{ ucfirst($laporan->status) }}</span></div>
                        <p class="mt-2 text-sm text-slate-600">{{ $laporan->keluhan }}</p>
                        @if ($laporan->status !== 'selesai')
                            <form method="POST" action="{{ route('admin.report.close', $laporan) }}" class="mt-3">@csrf<button class="rounded-lg bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-800">Tandai Selesai</button></form>
                        @endif
                    </article>
                @empty
                    <p class="text-sm text-slate-500">Belum ada laporan.</p>
                @endforelse
            </div>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <h2 class="text-xl font-bold">Pesanan Layanan</h2>
            <div class="mt-4 space-y-3">
                @forelse ($layanans as $layanan)
                    <article class="rounded-xl border p-4">
                        <div class="flex justify-between gap-3"><strong>{{ $layanan->jenis_layanan }}</strong><span class="text-xs text-slate-500">{{ ucfirst($layanan->status) }}</span></div>
                        <p class="mt-1 text-sm text-slate-600">{{ $layanan->user->nama }}@if($layanan->catatan) · {{ $layanan->catatan }}@endif</p>
                        @if ($layanan->status !== 'selesai')
                            <form method="POST" action="{{ route('admin.service.close', $layanan) }}" class="mt-3">@csrf<button class="rounded-lg bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-800">Tandai Selesai</button></form>
                        @endif
                    </article>
                @empty
                    <p class="text-sm text-slate-500">Belum ada pesanan layanan.</p>
                @endforelse
            </div>
        </div>
    </section>
@endsection
