<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use App\Models\Laporan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KoskuPageController extends Controller
{
    public function aturan(): View
    {
        return view('pages.aturan');
    }

    public function bantuan(Request $request): View
    {
        $laporans = Laporan::query()
            ->with('kamar')
            ->where('user_id', $request->user()->id)
            ->latest('tanggal_lapor')
            ->get();
        $kamars = Kamar::query()
            ->whereHas('pesanans', fn ($query) => $query
                ->where('user_id', $request->user()->id)
                ->where('status_pembayaran', 'lunas'))
            ->orderBy('nomor_kamar')
            ->get();

        return view('pages.bantuan', compact('laporans', 'kamars'));
    }

    public function storeLaporan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kamar_id' => ['required', 'integer', 'exists:kamars,id'],
            'keluhan' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        $isTenantRoom = Kamar::query()
            ->whereKey($validated['kamar_id'])
            ->whereHas('pesanans', fn ($query) => $query
                ->where('user_id', $request->user()->id)
                ->where('status_pembayaran', 'lunas'))
            ->exists();

        if (! $isTenantRoom) {
            return back()->withErrors(['kamar_id' => 'Pilih kamar yang pernah Anda pesan dan lunas.'])->withInput();
        }

        $request->user()->laporans()->create([
            ...$validated,
            'status' => 'baru',
            'tanggal_lapor' => now(),
        ]);

        return back()->with('status', 'Laporan berhasil dikirim kepada pengelola.');
    }

    public function faq(): View
    {
        return view('pages.faq');
    }

    public function fasilitas(): View
    {
        return view('pages.fasilitas');
    }

    public function layanan(): View
    {
        return view('pages.layanan');
    }

    public function storeLayanan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jenis_layanan' => ['required', 'in:Aqua refill,Camilan,Air mineral,Bersihkan kamar'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        $request->user()->pesananLayanans()->create([
            ...$validated,
            'status' => 'baru',
            'tanggal_pesan' => now(),
        ]);

        return back()->with('status', 'Pesanan layanan berhasil dikirim.');
    }
}
