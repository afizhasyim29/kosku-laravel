@extends('layouts.kosku')

@section('title', 'Pemindai Tiket - Kos-Ku')

@section('content')
    <section class="mx-auto max-w-2xl rounded-3xl bg-white p-6 shadow-sm sm:p-9">
        <p class="text-sm font-bold uppercase tracking-widest text-blue-700">Validasi tiket penghuni</p>
        <h1 class="mt-2 text-3xl font-extrabold">Pemindai QR</h1>
        <p class="mt-2 text-sm leading-6 text-slate-600">Arahkan kamera ke QR pada tiket penghuni. QR yang cocok dan sudah lunas akan menampilkan animasi konfirmasi masuk.</p>
        <div id="reader" class="mt-6 overflow-hidden rounded-xl"></div>
        <div class="mt-5 flex gap-2">
            <input id="ticket-code" class="min-w-0 flex-1 rounded-xl border-slate-300" placeholder="Atau masukkan kode tiket">
            <button id="validate-ticket" class="rounded-xl bg-blue-700 px-4 py-2 font-semibold text-white">Validasi</button>
        </div>
        <div id="scan-result" class="mt-4 hidden rounded-xl p-4 text-sm"></div>
    </section>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        const result = document.getElementById('scan-result');
        let isValidating = false;
        async function validateTicket(code) {
            if (!code || isValidating) return;
            isValidating = true;
            result.className = 'mt-4 rounded-xl bg-slate-100 p-4 text-sm';
            result.textContent = 'Memeriksa tiket...';
            result.classList.remove('hidden');
            try {
                const response = await fetch(@json(route('scanner.api')) + '?kode=' + encodeURIComponent(code), { credentials: 'same-origin' });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'Tiket tidak valid.');
                window.location.assign(@json(route('scanner.kamar-masuk')) + '?kode=' + encodeURIComponent(data.kode));
            } catch (error) {
                result.className = 'mt-4 rounded-xl bg-red-50 p-4 text-sm text-red-800';
                result.textContent = error.message;
                isValidating = false;
            }
        }
        document.getElementById('validate-ticket').addEventListener('click', () => validateTicket(document.getElementById('ticket-code').value.trim()));
        const scanner = new Html5Qrcode('reader');
        scanner.start({ facingMode: 'environment' }, { fps: 10, qrbox: { width: 240, height: 240 } }, (text) => {
            document.getElementById('ticket-code').value = text;
            scanner.stop().then(() => validateTicket(text)).catch(() => validateTicket(text));
        }).catch(() => {
            result.className = 'mt-4 rounded-xl bg-amber-50 p-4 text-sm text-amber-900';
            result.textContent = 'Kamera tidak dapat dibuka. Anda tetap dapat memasukkan kode tiket secara manual.';
            result.classList.remove('hidden');
        });
    </script>
@endsection
