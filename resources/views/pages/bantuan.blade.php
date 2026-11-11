@extends('layouts.kosku')

@section('title', 'Bantuan - Kos-Ku')

@section('content')
    <div class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
        <section class="rounded-2xl bg-white p-6 shadow-sm">
            <h1 class="text-2xl font-extrabold">Kirim Laporan Kendala</h1>
            <p class="mt-2 text-sm leading-6 text-slate-600">Laporan hanya dapat dibuat untuk kamar yang pernah Anda pesan dan sudah lunas.</p>
            @if ($kamars->isEmpty())
                <div class="mt-5 rounded-xl bg-amber-50 p-4 text-sm text-amber-800">Belum ada kamar sewaan aktif pada akun ini.</div>
            @else
                <form method="POST" action="{{ route('bantuan.store') }}" class="mt-5 space-y-4">
                    @csrf
                    <div>
                        <label for="kamar_id" class="mb-1 block text-sm font-semibold">Kamar</label>
                        <select id="kamar_id" name="kamar_id" required class="w-full rounded-xl border-slate-300">
                            @foreach ($kamars as $kamar)
                                <option value="{{ $kamar->id }}">{{ $kamar->nomor_kamar }} - {{ $kamar->tipe_kamar }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="keluhan" class="mb-1 block text-sm font-semibold">Jelaskan kendala</label>
                        <textarea id="keluhan" name="keluhan" rows="5" required maxlength="2000" class="w-full rounded-xl border-slate-300">{{ old('keluhan') }}</textarea>
                    </div>
                    <button class="rounded-xl bg-blue-700 px-5 py-3 font-bold text-white hover:bg-blue-800">Kirim Laporan</button>
                </form>
            @endif
        </section>
        <section class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="text-xl font-bold">Riwayat Laporan Saya</h2>
            <div class="mt-4 space-y-3">
                @forelse ($laporans as $laporan)
                    <article class="rounded-xl border border-slate-100 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="font-bold">Kamar {{ $laporan->kamar->nomor_kamar }}</h3>
                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-800">{{ ucfirst($laporan->status) }}</span>
                        </div>
                        <p class="mt-2 text-sm text-slate-600">{{ $laporan->keluhan }}</p>
                        <p class="mt-2 text-xs text-slate-400">{{ $laporan->tanggal_lapor->format('d M Y H:i') }}</p>
                    </article>
                @empty
                    <p class="rounded-xl bg-slate-50 p-4 text-sm text-slate-600">Belum ada laporan.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
