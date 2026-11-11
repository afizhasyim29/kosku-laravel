@extends('layouts.kosku')

@section('title', 'Pesanan Saya - Kos-Ku')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div><p class="text-sm font-bold uppercase tracking-widest text-blue-700">Akun penghuni</p><h1 class="mt-2 text-3xl font-extrabold">Pesanan Saya</h1></div>
        <a href="{{ route('resident.card') }}" class="rounded-xl border border-blue-200 bg-white px-4 py-2 font-semibold text-blue-800">Kartu Penghuni</a>
    </div>
    <div class="mt-6 space-y-4">
        @forelse ($pesanans as $pesanan)
            <article class="rounded-2xl bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div><p class="text-xs uppercase tracking-wider text-slate-400">{{ $pesanan->kode_tiket }}</p><h2 class="mt-1 text-xl font-bold">Kamar {{ $pesanan->kamar->nomor_kamar }} · {{ $pesanan->kamar->tipe_kamar }}</h2></div>
                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $pesanan->status_pembayaran === 'lunas' ? 'bg-emerald-100 text-emerald-800' : ($pesanan->status_pembayaran === 'ditolak' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">{{ str_replace('_', ' ', ucfirst($pesanan->status_pembayaran)) }}</span>
                </div>
                <div class="mt-4 grid gap-2 text-sm text-slate-600 sm:grid-cols-3">
                    <p>Masuk: {{ $pesanan->tanggal_masuk->format('d M Y') }}</p>
                    <p>Durasi: {{ $pesanan->durasi_bulan }} bulan</p>
                    <p>Total: Rp {{ number_format((float) $pesanan->total_bayar, 0, ',', '.') }}</p>
                    @if ($pesanan->status_pembayaran === 'lunas')
                        @php($tanggalAkhir = $pesanan->tanggal_masuk->copy()->addMonths($pesanan->durasi_bulan))
                        <p>Masa sewa berakhir: {{ $tanggalAkhir->format('d M Y') }}</p>
                        <p>Sisa masa sewa: {{ max(0, (int) now()->startOfDay()->diffInDays($tanggalAkhir, false)) }} hari</p>
                    @endif
                </div>
                <div class="mt-5 flex flex-wrap gap-2">
                    @if (in_array($pesanan->status_pembayaran, ['menunggu', 'menunggu_verifikasi'], true))
                        <a href="{{ route('payments.booking', $pesanan) }}" class="rounded-lg bg-blue-700 px-4 py-2 text-sm font-bold text-white">Lihat Pembayaran</a>
                    @endif
                    @if ($pesanan->status_pembayaran === 'lunas')
                        <a href="{{ route('orders.ticket', $pesanan->kode_tiket) }}" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white">Tiket Penghuni</a>
                        <a href="{{ route('orders.invoice', $pesanan->kode_tiket) }}" class="rounded-lg border px-4 py-2 text-sm font-semibold">Invoice</a>
                        <a href="{{ route('orders.receipt', $pesanan->kode_tiket) }}" class="rounded-lg border px-4 py-2 text-sm font-semibold">Kuitansi</a>
                        <a href="{{ route('orders.extend', $pesanan) }}" class="rounded-lg border border-blue-200 px-4 py-2 text-sm font-semibold text-blue-800">Perpanjang</a>
                    @endif
                </div>
                @if ($pesanan->perpanjangans->isNotEmpty())
                    <div class="mt-4 border-t pt-3 text-sm">
                        <p class="font-semibold">Riwayat perpanjangan</p>
                        @foreach ($pesanan->perpanjangans as $extension)
                            <p class="mt-1 text-slate-600">{{ $extension->durasi_bulan }} bulan · Rp {{ number_format((float) $extension->total_bayar, 0, ',', '.') }} · {{ str_replace('_', ' ', ucfirst($extension->status_pembayaran)) }}</p>
                        @endforeach
                    </div>
                @endif
            </article>
        @empty
            <div class="rounded-2xl bg-white p-8 text-center text-slate-600 shadow-sm">Belum ada pesanan kamar. <a class="font-semibold text-blue-700" href="{{ route('rooms.index') }}">Lihat katalog</a>.</div>
        @endforelse
    </div>
@endsection
