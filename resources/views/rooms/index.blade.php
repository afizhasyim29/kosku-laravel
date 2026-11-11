@extends('layouts.kosku')

@section('title', 'Katalog Kamar - Kos-Ku')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm font-bold uppercase tracking-widest text-blue-700">Temukan hunian nyaman</p>
            <h1 class="mt-2 text-3xl font-extrabold">Katalog Kamar</h1>
        </div>
        <a href="{{ route('booking.create') }}" class="rounded-xl bg-blue-700 px-5 py-3 font-bold text-white hover:bg-blue-800">Booking Sekarang</a>
    </div>
    <form method="GET" class="mt-6 grid gap-3 rounded-2xl bg-white p-4 shadow-sm sm:grid-cols-4">
        <input name="cari" value="{{ request('cari') }}" placeholder="Cari tipe atau nomor kamar" class="rounded-xl border-slate-300 sm:col-span-2">
        <select name="lantai" class="rounded-xl border-slate-300">
            <option value="semua">Semua lantai</option>
            @foreach (range(1, 5) as $floor)
                <option value="{{ $floor }}" @selected(request('lantai') == $floor)>Lantai {{ $floor }}</option>
            @endforeach
        </select>
        <select name="harga" class="rounded-xl border-slate-300">
            <option value="">Urutkan harga</option>
            <option value="termurah" @selected(request('harga') === 'termurah')>Termurah</option>
            <option value="termahal" @selected(request('harga') === 'termahal')>Termahal</option>
        </select>
        <button class="rounded-xl bg-slate-900 px-4 py-2 font-semibold text-white sm:col-span-4">Terapkan Filter</button>
    </form>

    <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($kamars as $kamar)
            <article class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
                <div class="flex items-center justify-between bg-gradient-to-r from-blue-950 to-blue-700 p-5 text-white">
                    <div><p class="text-xs uppercase tracking-widest text-blue-200">Kamar {{ $kamar->nomor_kamar }}</p><h2 class="mt-1 text-xl font-bold">{{ $kamar->tipe_kamar }}</h2></div>
                    <i class="fa-solid fa-bed text-3xl text-blue-200"></i>
                </div>
                <div class="p-5">
                    <p class="text-sm text-slate-600">{{ \Illuminate\Support\Str::limit($kamar->fasilitas ?: 'Fasilitas nyaman untuk kebutuhan harian.', 100) }}</p>
                    <p class="mt-4 text-xl font-extrabold text-blue-800">Rp {{ number_format((float) $kamar->harga, 0, ',', '.') }}<span class="text-sm font-normal text-slate-500"> / bulan</span></p>
                    <span class="mt-3 inline-flex rounded-full px-3 py-1 text-xs font-bold {{ in_array($kamar->status, ['tersedia', 'kosong'], true) ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">{{ ucfirst($kamar->status) }}</span>
                    <div class="mt-5 flex gap-2">
                        <a href="{{ route('rooms.show', $kamar) }}" class="flex-1 rounded-xl border border-blue-200 px-3 py-2 text-center text-sm font-semibold text-blue-800 hover:bg-blue-50">Detail</a>
                        @if (in_array($kamar->status, ['tersedia', 'kosong'], true))
                            <a href="{{ route('booking.create', ['kamar_id' => $kamar->id]) }}" class="flex-1 rounded-xl bg-blue-700 px-3 py-2 text-center text-sm font-semibold text-white hover:bg-blue-800">Pilih</a>
                        @else
                            <span class="flex-1 rounded-xl bg-slate-100 px-3 py-2 text-center text-sm text-slate-400">Tidak tersedia</span>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <p class="rounded-2xl bg-white p-6 text-slate-600 sm:col-span-2 lg:col-span-3">Tidak ada kamar yang sesuai dengan filter.</p>
        @endforelse
    </div>
    <div class="mt-6">{{ $kamars->links() }}</div>
@endsection
