@extends('layouts.kosku')

@section('title', 'Pembayaran QRIS - Kos-Ku')

@section('content')
    <div class="mx-auto grid max-w-5xl gap-6 lg:grid-cols-[0.85fr_1.15fr]">
        <section class="rounded-3xl bg-white p-6 shadow-sm sm:p-8">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-700">Pembayaran pesanan</p>
            <h1 class="mt-2 text-2xl font-extrabold">Ringkasan Booking</h1>
            <dl class="mt-6 space-y-4 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-slate-500">Kode pesanan</dt><dd class="font-bold">{{ $pesanan->kode_tiket }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">Kamar</dt><dd class="font-semibold">{{ $pesanan->kamar->nomor_kamar }} · {{ $pesanan->kamar->tipe_kamar }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">Tanggal masuk</dt><dd class="font-semibold">{{ $pesanan->tanggal_masuk->format('d M Y') }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-500">Durasi</dt><dd class="font-semibold">{{ $pesanan->durasi_bulan }} bulan</dd></div>
                <div class="flex justify-between gap-4 border-t pt-4"><dt class="font-bold">Total</dt><dd class="text-lg font-extrabold text-blue-800">Rp {{ number_format((float) $pesanan->total_bayar, 0, ',', '.') }}</dd></div>
            </dl>
        </section>
        <section class="rounded-3xl bg-white p-6 text-center shadow-sm sm:p-8">
            <h2 class="text-xl font-extrabold">Bayar melalui QRIS</h2>
            <p class="mt-2 text-sm text-slate-600">Ini adalah simulasi pembayaran untuk demo. Tekan tombol setelah simulasi selesai untuk mengaktifkan pesanan dan tiket penghuni.</p>
            <img src="{{ asset('images/qris.jpg') }}" alt="QRIS pembayaran Kos-Ku" class="mx-auto mt-5 max-h-80 rounded-xl border bg-white object-contain p-2">
            <p class="mt-4 rounded-xl bg-slate-50 p-3 text-sm">Kode referensi: <strong>{{ $pesanan->kode_tiket }}</strong></p>
            @if ($pesanan->status_pembayaran === 'menunggu')
                <form method="POST" action="{{ route('payments.booking.confirm', $pesanan) }}" class="mt-5">
                    @csrf
                    <button class="w-full rounded-xl bg-emerald-700 px-5 py-3 font-bold text-white hover:bg-emerald-800">Simulasikan Pembayaran Berhasil</button>
                </form>
            @else
                <p class="mt-5 rounded-xl bg-amber-50 p-4 text-sm text-amber-800">Pesanan saat ini berstatus {{ str_replace('_', ' ', $pesanan->status_pembayaran) }}.</p>
            @endif
            <p class="mt-4 text-xs leading-5 text-slate-500">Mode demo: tombol ini langsung menandai pembayaran lunas. Untuk menerima pembayaran sungguhan, aplikasi perlu integrasi payment gateway.</p>
        </section>
    </div>
@endsection
