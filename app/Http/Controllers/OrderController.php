<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $pesanans = Pesanan::query()
            ->with(['kamar', 'perpanjangans'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return view('orders.index', compact('pesanans'));
    }

    public function invoice(Request $request, string $kode): View
    {
        $pesanan = $this->ownedOrder($request, $kode);

        return view('orders.invoice', compact('pesanan'));
    }

    public function kuitansi(Request $request, string $kode): View
    {
        $pesanan = $this->ownedOrder($request, $kode);
        abort_unless($pesanan->status_pembayaran === 'lunas', 403);

        return view('orders.kuitansi', compact('pesanan'));
    }

    public function ticket(Request $request, string $kode): View
    {
        $pesanan = $this->ownedOrder($request, $kode);
        abort_unless($pesanan->status_pembayaran === 'lunas', 403);

        return view('orders.tiket', compact('pesanan'));
    }

    public function kartuPenghuni(Request $request): View
    {
        $pesanan = Pesanan::query()
            ->with('kamar')
            ->where('user_id', $request->user()->id)
            ->where('status_pembayaran', 'lunas')
            ->latest('paid_at')
            ->first();

        return view('orders.kartu-penghuni', compact('pesanan'));
    }

    public function extend(Request $request, Pesanan $pesanan): View
    {
        abort_unless($pesanan->user_id === $request->user()->id, 404);
        abort_unless($pesanan->status_pembayaran === 'lunas', 403);

        return view('orders.extend', compact('pesanan'));
    }

    public function storeExtension(Request $request, Pesanan $pesanan): RedirectResponse
    {
        abort_unless($pesanan->user_id === $request->user()->id, 404);
        abort_unless($pesanan->status_pembayaran === 'lunas', 403);

        $validated = $request->validate([
            'durasi_bulan' => ['required', 'integer', 'in:1,3,6'],
        ]);

        $extension = DB::transaction(function () use ($pesanan, $validated, $request) {
            $lockedOrder = Pesanan::query()->lockForUpdate()->findOrFail($pesanan->id);
            abort_unless($lockedOrder->user_id === $request->user()->id, 404);
            abort_unless($lockedOrder->status_pembayaran === 'lunas', 403);

            $months = (int) $validated['durasi_bulan'];

            return $lockedOrder->perpanjangans()->create([
                'user_id' => $request->user()->id,
                'durasi_bulan' => $months,
                'total_bayar' => (float) $lockedOrder->kamar->harga * $months,
                'status_pembayaran' => 'menunggu_pembayaran',
                'kode_tiket' => 'EXT-KK-'.Str::upper(Str::random(10)),
            ]);
        });

        return redirect()->route('extensions.payment', $extension);
    }

    private function ownedOrder(Request $request, string $kode): Pesanan
    {
        return Pesanan::query()
            ->with('kamar', 'user')
            ->where('kode_tiket', $kode)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
    }
}
