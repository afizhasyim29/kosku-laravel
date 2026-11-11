<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScanController extends Controller
{
    public function index(Request $request): View
    {
        return view('scanner.index');
    }

    public function scan(Request $request): JsonResponse
    {
        $validated = $request->validate(['kode' => ['required', 'string', 'max:100']]);
        $pesanan = Pesanan::query()
            ->with(['user', 'kamar'])
            ->where('kode_tiket', $validated['kode'])
            ->where('status_pembayaran', 'lunas')
            ->first();

        if (! $pesanan) {
            return response()->json(['success' => false, 'message' => 'Tiket tidak ditemukan atau belum lunas.'], 404);
        }

        return response()->json([
            'success' => true,
            'penghuni' => $pesanan->user->nama,
            'kamar' => $pesanan->kamar->nomor_kamar,
            'tipe' => $pesanan->kamar->tipe_kamar,
            'kode' => $pesanan->kode_tiket,
        ]);
    }

    public function kamarMasuk(Request $request): View
    {
        $validated = $request->validate(['kode' => ['required', 'string', 'max:100']]);
        $pesanan = Pesanan::query()
            ->with(['user', 'kamar'])
            ->where('kode_tiket', $validated['kode'])
            ->where('status_pembayaran', 'lunas')
            ->firstOrFail();

        return view('scanner.kamar-masuk', compact('pesanan'));
    }
}
