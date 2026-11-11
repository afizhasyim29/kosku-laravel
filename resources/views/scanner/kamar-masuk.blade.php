@extends('layouts.kosku')

@section('title', 'Informasi Kedatangan - Kos-Ku')

@section('content')
    <style>
        @keyframes success-pop { 0% { opacity: 0; transform: scale(.65) rotate(-12deg); } 70% { transform: scale(1.12) rotate(4deg); } 100% { opacity: 1; transform: scale(1) rotate(0); } }
        @keyframes door-open { 0% { transform: perspective(300px) rotateY(0); } 100% { transform: perspective(300px) rotateY(-62deg); } }
        @keyframes welcome-rise { 0% { opacity: 0; transform: translateY(16px); } 100% { opacity: 1; transform: translateY(0); } }
        @keyframes confetti-drop { 0% { opacity: 0; transform: translateY(-18px) rotate(0); } 30% { opacity: 1; } 100% { opacity: 0; transform: translateY(70px) rotate(160deg); } }
        .success-pop { animation: success-pop .75s cubic-bezier(.2,.8,.2,1) both; }
        .door-open { transform-origin: left center; animation: door-open .85s .25s ease-in-out both; }
        .welcome-rise { animation: welcome-rise .6s .45s ease-out both; }
        .confetti-piece { animation: confetti-drop 1.8s ease-out both; }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; scroll-behavior: auto !important; } }
    </style>
    <section class="relative mx-auto max-w-2xl overflow-hidden rounded-3xl bg-white p-8 text-center shadow-xl sm:p-12">
        <span class="confetti-piece absolute left-[18%] top-12 h-3 w-3 rounded-sm bg-amber-400"></span>
        <span class="confetti-piece absolute left-[34%] top-8 h-3 w-3 rounded-full bg-blue-500" style="animation-delay:.15s"></span>
        <span class="confetti-piece absolute right-[32%] top-10 h-3 w-3 rounded-sm bg-pink-500" style="animation-delay:.3s"></span>
        <span class="confetti-piece absolute right-[17%] top-14 h-3 w-3 rounded-full bg-emerald-500" style="animation-delay:.1s"></span>
        <div class="success-pop mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-emerald-100 text-5xl text-emerald-700 shadow-inner">
            <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
        </div>
        <p class="welcome-rise mt-6 text-sm font-bold uppercase tracking-[0.2em] text-emerald-700">Pembayaran Lunas · Tiket Valid</p>
        <h1 class="welcome-rise mt-3 text-3xl font-extrabold text-slate-900 sm:text-4xl">Silakan Masuk!</h1>
        <p class="welcome-rise mt-3 text-lg text-slate-600">Selamat datang di Kos-Ku, {{ $pesanan->user->nama }}.</p>
        <div class="welcome-rise mx-auto mt-8 flex max-w-sm items-center justify-center gap-5 rounded-2xl bg-blue-50 p-5">
            <div class="relative flex h-20 w-20 items-center justify-center rounded-t-lg bg-blue-200 text-4xl text-blue-800">
                <i class="fa-solid fa-house" aria-hidden="true"></i>
                <span class="door-open absolute bottom-0 right-5 h-9 w-6 rounded-t bg-amber-600 shadow"></span>
            </div>
            <div class="text-left">
                <p class="text-xs font-bold uppercase tracking-wider text-blue-600">Kamar penghuni</p>
                <p class="mt-1 text-2xl font-extrabold text-blue-950">{{ $pesanan->kamar->nomor_kamar }}</p>
                <p class="text-sm text-slate-600">{{ $pesanan->kamar->tipe_kamar }}</p>
            </div>
        </div>
        <p class="welcome-rise mt-6 font-mono text-sm text-slate-500">{{ $pesanan->kode_tiket }}</p>
        <a href="{{ route('scanner.index') }}" class="welcome-rise mt-8 inline-flex rounded-xl bg-blue-700 px-5 py-3 font-bold text-white transition hover:bg-blue-800">Scan Tiket Berikutnya</a>
        <p class="mt-6 text-xs text-slate-400">Konfirmasi ini memvalidasi pembayaran dan tiket. Tidak ada kunci fisik yang dikendalikan aplikasi.</p>
    </section>
@endsection
