<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\PerpanjanganPesanan;
use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function booking(Request $request, Pesanan $pesanan): View
    {
        abort_unless($pesanan->user_id === $request->user()->id, 404);
        abort_unless(in_array($pesanan->status_pembayaran, ['menunggu', 'menunggu_verifikasi'], true), 403);

        return view('payments.booking', compact('pesanan'));
    }

    public function confirmBooking(Request $request, Pesanan $pesanan): RedirectResponse
    {
        abort_unless($pesanan->user_id === $request->user()->id, 404);

        DB::transaction(function () use ($pesanan): void {
            $lockedOrder = Pesanan::query()->lockForUpdate()->findOrFail($pesanan->id);

            if ($lockedOrder->status_pembayaran === 'lunas') {
                return;
            }

            if ($lockedOrder->status_pembayaran !== 'menunggu') {
                throw ValidationException::withMessages([
                    'pembayaran' => 'Pesanan ini tidak dapat dibayar pada status saat ini.',
                ]);
            }

            $room = Kamar::query()->lockForUpdate()->findOrFail($lockedOrder->kamar_id);
            $otherPaidOrderExists = Pesanan::query()
                ->where('kamar_id', $room->id)
                ->where('id', '!=', $lockedOrder->id)
                ->where('status_pembayaran', 'lunas')
                ->exists();

            if ($otherPaidOrderExists) {
                throw ValidationException::withMessages([
                    'pembayaran' => 'Kamar ini sudah dipesan penghuni lain. Hubungi pengelola untuk bantuan.',
                ]);
            }

            $lockedOrder->update([
                'status_pembayaran' => 'lunas',
                'paid_at' => now(),
            ]);
            $room->update(['status' => 'terisi']);
        });

        return redirect()->route('orders.ticket', $pesanan->kode_tiket)
            ->with('status', 'Pembayaran simulasi berhasil. Tiket penghuni sudah aktif.');
    }

    public function extension(Request $request, PerpanjanganPesanan $perpanjangan): View
    {
        abort_unless($perpanjangan->user_id === $request->user()->id, 404);
        abort_unless(in_array($perpanjangan->status_pembayaran, ['menunggu_pembayaran', 'menunggu_verifikasi'], true), 403);

        return view('payments.extension', compact('perpanjangan'));
    }

    public function confirmExtension(Request $request, PerpanjanganPesanan $perpanjangan): RedirectResponse
    {
        abort_unless($perpanjangan->user_id === $request->user()->id, 404);

        DB::transaction(function () use ($perpanjangan, $request): void {
            $lockedExtension = PerpanjanganPesanan::query()->lockForUpdate()->findOrFail($perpanjangan->id);

            if ($lockedExtension->user_id !== $request->user()->id) {
                abort(404);
            }

            if ($lockedExtension->status_pembayaran === 'lunas') {
                return;
            }

            if ($lockedExtension->status_pembayaran !== 'menunggu_pembayaran') {
                throw ValidationException::withMessages([
                    'pembayaran' => 'Perpanjangan ini tidak dapat dibayar pada status saat ini.',
                ]);
            }

            $order = Pesanan::query()->lockForUpdate()->findOrFail($lockedExtension->pesanan_id);
            if ($order->user_id !== $lockedExtension->user_id || $order->status_pembayaran !== 'lunas') {
                throw ValidationException::withMessages([
                    'pembayaran' => 'Pesanan sewa tidak lagi aktif. Hubungi pengelola untuk bantuan.',
                ]);
            }

            $order->increment('durasi_bulan', $lockedExtension->durasi_bulan);
            $order->increment('total_bayar', $lockedExtension->total_bayar);
            $lockedExtension->update([
                'status_pembayaran' => 'lunas',
                'paid_at' => now(),
            ]);
        });

        return redirect()->route('orders.index')
            ->with('status', 'Pembayaran perpanjangan simulasi berhasil. Masa sewa dan bukti pesanan sudah diperbarui.');
    }
}
