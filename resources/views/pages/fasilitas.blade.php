@extends('layouts.kosku')

@section('title', 'Fasilitas - Kos-Ku')

@section('content')
    <p class="text-sm font-bold uppercase tracking-widest text-blue-700">Kenyamanan penghuni</p>
    <h1 class="mt-2 text-3xl font-extrabold">Fasilitas Kos-Ku</h1>
    <div class="mt-7 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ([
            ['fa-car', 'Area Parkir', 'Area parkir untuk penghuni dan tamu.'],
            ['fa-utensils', 'Dapur Bersama', 'Ruang dapur bersama yang dapat digunakan penghuni.'],
            ['fa-shield-halved', 'Keamanan', 'Area hunian terjaga untuk kenyamanan bersama.'],
            ['fa-couch', 'Ruang Santai', 'Area bersama untuk bersantai dan berinteraksi.'],
            ['fa-shirt', 'Area Cuci dan Jemur', 'Ruang untuk kebutuhan mencuci dan menjemur pakaian.'],
            ['fa-wifi', 'Wi-Fi', 'Akses internet untuk menunjang kegiatan sehari-hari.'],
        ] as [$icon, $title, $description])
            <article class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-xl text-blue-700"><i class="fa-solid {{ $icon }}"></i></div>
                <h2 class="mt-4 text-lg font-bold">{{ $title }}</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">{{ $description }}</p>
            </article>
        @endforeach
    </div>
@endsection
