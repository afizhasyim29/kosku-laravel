@extends('layouts.kosku')

@section('title', 'Aturan Hunian - Kos-Ku')

@section('content')
    <section class="mx-auto max-w-3xl rounded-3xl bg-white p-6 shadow-sm sm:p-10">
        <p class="text-sm font-bold uppercase tracking-widest text-blue-700">Panduan penghuni</p>
        <h1 class="mt-2 text-3xl font-extrabold">Aturan Hunian</h1>
        <ol class="mt-7 list-decimal space-y-4 pl-5 leading-7 text-slate-600">
            <li>Jaga kebersihan kamar dan area bersama.</li>
            <li>Hormati kenyamanan dan privasi penghuni lain.</li>
            <li>Hindari suara keras pada waktu istirahat malam.</li>
            <li>Jaga barang pribadi dan pastikan pintu terkunci saat meninggalkan kamar.</li>
            <li>Jangan membawa atau menyimpan barang yang membahayakan penghuni.</li>
            <li>Laporkan kerusakan atau kendala fasilitas melalui halaman Bantuan.</li>
        </ol>
        <a href="{{ route('rooms.index') }}" class="mt-8 inline-flex rounded-xl bg-blue-700 px-5 py-3 font-bold text-white hover:bg-blue-800">Lihat Katalog Kamar</a>
    </section>
@endsection
