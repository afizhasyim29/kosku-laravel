@extends('layouts.kosku')

@section('title', 'FAQ - Kos-Ku')

@section('content')
    <section class="mx-auto max-w-3xl">
        <p class="text-sm font-bold uppercase tracking-widest text-blue-700">Pusat informasi</p>
        <h1 class="mt-2 text-3xl font-extrabold">Pertanyaan yang Sering Ditanyakan</h1>
        <div class="mt-7 space-y-4">
            @foreach ([
                ['Bagaimana cara memesan kamar?', 'Masuk ke akun, pilih kamar yang tersedia, tentukan tanggal masuk dan durasi, lalu ikuti instruksi pembayaran.'],
                ['Bagaimana status pembayaran diperbarui?', 'Konfirmasi pembayaran akan diperiksa pengelola. Status berubah menjadi lunas setelah pembayaran diverifikasi.'],
                ['Bagaimana cara memperpanjang sewa?', 'Buka menu Pesanan, pilih Perpanjang pada pesanan lunas, lalu ikuti langkah pembayaran.'],
                ['Bagaimana melaporkan kendala kamar?', 'Buka menu Bantuan, pilih kamar yang Anda sewa, tulis kendala, lalu kirim laporan.'],
                ['Bagaimana memesan layanan tambahan?', 'Pilih menu Layanan, pilih jenis layanan, dan kirim pesanan dengan catatan bila diperlukan.'],
            ] as [$question, $answer])
                <details class="rounded-2xl bg-white p-5 shadow-sm">
                    <summary class="cursor-pointer font-bold">{{ $question }}</summary>
                    <p class="mt-3 leading-7 text-slate-600">{{ $answer }}</p>
                </details>
            @endforeach
        </div>
    </section>
@endsection
