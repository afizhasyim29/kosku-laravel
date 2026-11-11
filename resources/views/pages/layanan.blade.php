@extends('layouts.kosku')

@section('title', 'Layanan - Kos-Ku')

@section('content')
    <section class="mx-auto max-w-2xl rounded-3xl bg-white p-6 shadow-sm sm:p-9">
        <p class="text-sm font-bold uppercase tracking-widest text-blue-700">Layanan penghuni</p>
        <h1 class="mt-2 text-3xl font-extrabold">Pesan Layanan Tambahan</h1>
        <form method="POST" action="{{ route('layanan.store') }}" class="mt-7 space-y-5">
            @csrf
            <div>
                <label for="jenis_layanan" class="mb-1 block text-sm font-semibold">Pilih layanan</label>
                <select id="jenis_layanan" name="jenis_layanan" required class="w-full rounded-xl border-slate-300">
                    @foreach (['Aqua refill', 'Camilan', 'Air mineral', 'Bersihkan kamar'] as $service)
                        <option value="{{ $service }}" @selected(old('jenis_layanan') === $service)>{{ $service }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="catatan" class="mb-1 block text-sm font-semibold">Catatan (opsional)</label>
                <textarea id="catatan" name="catatan" rows="3" maxlength="1000" class="w-full rounded-xl border-slate-300">{{ old('catatan') }}</textarea>
            </div>
            <button class="rounded-xl bg-blue-700 px-6 py-3 font-bold text-white hover:bg-blue-800">Pesan Layanan</button>
        </form>
    </section>
@endsection
