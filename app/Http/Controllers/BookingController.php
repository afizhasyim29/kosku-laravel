<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function create(Request $request): View
    {
        return view('booking', [
            'kamars' => Kamar::query()
                ->whereIn('status', ['tersedia', 'kosong'])
                ->orderBy('nomor_kamar')
                ->get(),
            'selectedKamarId' => $request->integer('kamar_id'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kamar_id' => ['required', 'integer', 'exists:kamars,id'],
            'tanggal_masuk' => ['required', 'date', 'after_or_equal:today'],
            'durasi_bulan' => ['required', 'integer', 'in:1,3,6'],
        ]);

        $pesanan = DB::transaction(function () use ($validated, $request): Pesanan {
            $kamar = Kamar::query()
                ->lockForUpdate()
                ->findOrFail($validated['kamar_id']);

            if (! in_array($kamar->status, ['tersedia', 'kosong'], true)) {
                throw ValidationException::withMessages([
                    'kamar_id' => 'Kamar tersebut sudah tidak tersedia. Silakan pilih kamar lain.',
                ]);
            }

            $durasi = (int) $validated['durasi_bulan'];
            $diskon = match ($durasi) {
                3 => 0.05,
                6 => 0.10,
                default => 0,
            };
            $total = round((float) $kamar->harga * $durasi * (1 - $diskon), 2);

            $pesanan = Pesanan::query()->create([
                'user_id' => $request->user()->id,
                'kamar_id' => $kamar->id,
                'tanggal_masuk' => $validated['tanggal_masuk'],
                'durasi_bulan' => $durasi,
                'total_bayar' => $total,
                'status_pembayaran' => 'menunggu',
                'kode_tiket' => 'INV-KK-'.Str::upper(Str::random(10)),
            ]);

            $kamar->update(['status' => 'dipesan']);

            return $pesanan;
        });

        return redirect()->route('payments.booking', $pesanan);
    }
}
