<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\PerpanjanganPesanan;
use App\Models\Pesanan;
use App\Models\PesananLayanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureAdmin($request);

        return view('admin.index', [
            'laporans' => Laporan::query()->with(['user', 'kamar'])->latest('tanggal_lapor')->get(),
            'layanans' => PesananLayanan::query()->with('user')->latest('tanggal_pesan')->get(),
            'pesanans' => Pesanan::query()->with(['user', 'kamar'])
                ->where('status_pembayaran', 'menunggu_verifikasi')->latest()->get(),
            'perpanjangans' => PerpanjanganPesanan::query()->with(['user', 'pesanan.kamar'])
                ->where('status_pembayaran', 'menunggu_verifikasi')->latest()->get(),
        ]);
    }

    public function bookingPayment(Request $request, Pesanan $pesanan): RedirectResponse
    {
        $this->ensureAdmin($request);
        $validated = $request->validate(['status' => ['required', 'in:lunas,ditolak']]);

        DB::transaction(function () use ($pesanan, $validated): void {
            $order = Pesanan::query()->with('kamar')->lockForUpdate()->findOrFail($pesanan->id);
            abort_unless($order->status_pembayaran === 'menunggu_verifikasi', 409);

            if ($validated['status'] === 'lunas') {
                $otherPaidOrderExists = Pesanan::query()
                    ->where('kamar_id', $order->kamar_id)
                    ->where('id', '!=', $order->id)
                    ->where('status_pembayaran', 'lunas')
                    ->exists();
                abort_if($otherPaidOrderExists, 409, 'Kamar sudah memiliki penghuni aktif.');

                $order->update(['status_pembayaran' => 'lunas', 'paid_at' => now()]);
                $order->kamar->update(['status' => 'terisi']);
            } else {
                $order->update(['status_pembayaran' => 'ditolak']);

                $otherPendingOrPaid = Pesanan::query()
                    ->where('kamar_id', $order->kamar_id)
                    ->where('id', '!=', $order->id)
                    ->whereIn('status_pembayaran', ['lunas', 'menunggu', 'menunggu_verifikasi'])
                    ->exists();

                if (! $otherPendingOrPaid) {
                    $order->kamar->update(['status' => 'tersedia']);
                }
            }
        });

        return back()->with('status', 'Status pembayaran pesanan berhasil diperbarui.');
    }

    public function extensionPayment(Request $request, PerpanjanganPesanan $perpanjangan): RedirectResponse
    {
        $this->ensureAdmin($request);
        $validated = $request->validate(['status' => ['required', 'in:lunas,ditolak']]);

        DB::transaction(function () use ($perpanjangan, $validated): void {
            $extension = PerpanjanganPesanan::query()->with('pesanan')->lockForUpdate()->findOrFail($perpanjangan->id);
            abort_unless($extension->status_pembayaran === 'menunggu_verifikasi', 409);

            if ($validated['status'] === 'lunas') {
                $order = Pesanan::query()->lockForUpdate()->findOrFail($extension->pesanan_id);
                abort_unless($order->user_id === $extension->user_id && $order->status_pembayaran === 'lunas', 409);
                $order->increment('durasi_bulan', $extension->durasi_bulan);
                $order->increment('total_bayar', $extension->total_bayar);
                $extension->update(['status_pembayaran' => 'lunas', 'paid_at' => now()]);
            } else {
                $extension->update(['status_pembayaran' => 'ditolak']);
            }
        });

        return back()->with('status', 'Status pembayaran perpanjangan berhasil diperbarui.');
    }

    public function closeReport(Request $request, Laporan $laporan): RedirectResponse
    {
        $this->ensureAdmin($request);
        $laporan->update(['status' => 'selesai']);

        return back()->with('status', 'Laporan ditandai selesai.');
    }

    public function closeService(Request $request, PesananLayanan $pesananLayanan): RedirectResponse
    {
        $this->ensureAdmin($request);
        $pesananLayanan->update(['status' => 'selesai']);

        return back()->with('status', 'Pesanan layanan ditandai selesai.');
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->isAdmin(), 403);
    }
}
