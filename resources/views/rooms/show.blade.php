@extends('layouts.kosku')

@section('title', 'Detail Kamar '.$kamar->nomor_kamar.' - Kos-Ku')

@section('content')
    <div class="mx-auto grid max-w-5xl gap-6 lg:grid-cols-[1.2fr_0.8fr]">
        <section class="rounded-3xl bg-white p-6 shadow-sm sm:p-9">
            <p class="text-sm font-bold uppercase tracking-widest text-blue-700">Detail kamar</p>
            <h1 class="mt-2 text-3xl font-extrabold">Kamar {{ $kamar->nomor_kamar }}</h1>
            <p class="mt-1 text-lg text-slate-500">{{ $kamar->tipe_kamar }} · Lantai {{ substr($kamar->nomor_kamar, 0, 1) }}</p>
            <div class="mt-6 rounded-2xl bg-blue-50 p-5">
                <p class="text-sm font-semibold text-blue-900">Fasilitas</p>
                <div class="mt-3 flex flex-wrap gap-2">
                    @forelse (preg_split('/[,;]+/', $kamar->fasilitas ?? '', -1, PREG_SPLIT_NO_EMPTY) as $facility)
                        <span class="rounded-full bg-white px-3 py-1 text-sm text-blue-900">{{ trim($facility) }}</span>
                    @empty
                        <span class="text-sm text-slate-600">Fasilitas akan dikonfirmasi oleh pengelola.</span>
                    @endforelse
                </div>
            </div>
            <p class="mt-5 leading-7 text-slate-600">Kamar {{ $kamar->nomor_kamar }} adalah pilihan {{ strtolower($kamar->tipe_kamar) }} yang dirancang untuk hunian nyaman dan praktis, dengan fasilitas yang mendukung kebutuhan sehari-hari.</p>
            <div class="mt-6">
                <h2 class="font-bold">Simulasi sewa</h2>
                <label for="duration" class="mt-3 block text-sm text-slate-600">Pilih durasi untuk melihat estimasi:</label>
                <select id="duration" class="mt-2 w-full rounded-xl border-slate-300 sm:max-w-xs">
                    <option value="1">1 bulan</option>
                    <option value="3">3 bulan (diskon 5%)</option>
                    <option value="6">6 bulan (diskon 10%)</option>
                </select>
                <p class="mt-2 text-sm text-slate-600">Estimasi total: <strong id="duration-total" class="text-blue-800"></strong></p>
                <div class="mt-3 grid gap-3 sm:grid-cols-3">
                    @foreach ([1 => 0, 3 => 5, 6 => 10] as $month => $discount)
                        <div class="rounded-xl border border-slate-200 p-4">
                            <p class="text-sm text-slate-500">{{ $month }} bulan @if($discount) · hemat {{ $discount }}%@endif</p>
                            <p class="mt-1 font-bold">Rp {{ number_format((float) $kamar->harga * $month * (1 - $discount / 100), 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="mt-7 flex flex-wrap gap-3">
                <a href="{{ route('rooms.index') }}" class="rounded-xl border border-slate-300 px-5 py-3 font-semibold">Kembali ke katalog</a>
                <button id="share-room" type="button" class="rounded-xl border border-blue-200 px-5 py-3 font-semibold text-blue-800">Bagikan Kamar</button>
                @if (in_array($kamar->status, ['tersedia', 'kosong'], true))
                    <a href="{{ route('booking.create', ['kamar_id' => $kamar->id]) }}" class="rounded-xl bg-blue-700 px-5 py-3 font-bold text-white">Pesan kamar</a>
                @else
                    <span class="rounded-xl bg-slate-100 px-5 py-3 font-semibold text-slate-500">Kamar tidak tersedia</span>
                @endif
            </div>
            <p id="share-status" class="mt-2 text-sm text-slate-500" role="status"></p>
            <p class="mt-5 rounded-xl bg-amber-50 p-4 text-xs leading-5 text-amber-900">Pembayaran hanya dilakukan melalui halaman QRIS resmi setelah membuat pesanan. Pengelola akan memverifikasi pembayaran sebelum kamar dinyatakan lunas.</p>
        </section>
        <aside class="rounded-3xl bg-slate-900 p-6 text-white">
            <p class="text-sm text-blue-200">Harga bulanan</p>
            <p class="mt-2 text-3xl font-extrabold">Rp {{ number_format((float) $kamar->harga, 0, ',', '.') }}</p>
            <p class="mt-3 text-sm text-slate-300">Status: {{ ucfirst($kamar->status) }}</p>
            <p class="mt-7 text-sm leading-6 text-slate-300">Harga akhir dihitung server berdasarkan durasi sewa yang dipilih. Pembayaran akan ditinjau sebelum pesanan dinyatakan lunas.</p>
        </aside>
    </div>
    <section class="mx-auto mt-6 max-w-5xl rounded-3xl bg-white p-6 shadow-sm">
        <h2 class="text-xl font-bold">Lokasi Kos</h2>
        <p class="mt-1 text-sm text-slate-500">Peta ini merupakan ilustrasi lokasi sekitar kampus.</p>
        <iframe title="Peta lokasi Kos-Ku" class="mt-4 h-80 w-full rounded-2xl border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://maps.google.com/maps?q=-6.3438,106.7386&t=&z=16&ie=UTF8&iwloc=&output=embed"></iframe>
    </section>
    <script>
        const basePrice = Number(@json((float) $kamar->harga));
        const durationSelect = document.getElementById('duration');
        const totalElement = document.getElementById('duration-total');
        function updateEstimate() {
            const months = Number(durationSelect.value);
            const discount = months === 3 ? 0.05 : months === 6 ? 0.10 : 0;
            totalElement.textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(basePrice * months * (1 - discount));
        }
        durationSelect.addEventListener('change', updateEstimate);
        updateEstimate();

        document.getElementById('share-room').addEventListener('click', async () => {
            const details = { title: document.title, text: 'Lihat kamar {{ $kamar->nomor_kamar }} di Kos-Ku', url: window.location.href };
            const status = document.getElementById('share-status');
            try {
                if (navigator.share) await navigator.share(details);
                else {
                    await navigator.clipboard.writeText(details.url);
                    status.textContent = 'Tautan kamar disalin.';
                }
            } catch (error) {
                if (error.name !== 'AbortError') status.textContent = 'Tautan tidak dapat dibagikan dari browser ini.';
            }
        });
    </script>
@endsection
