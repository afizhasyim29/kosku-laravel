<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomController extends Controller
{
    public function index(Request $request): View
    {
        $query = Kamar::query();

        if ($request->filled('lantai') && $request->string('lantai')->toString() !== 'semua') {
            $floor = $request->string('lantai')->toString();
            $query->where('nomor_kamar', 'like', $floor.'%');
        }

        if ($request->filled('cari')) {
            $search = $request->string('cari')->toString();
            $query->where(function ($rooms) use ($search): void {
                $rooms->where('tipe_kamar', 'like', '%'.$search.'%')
                    ->orWhere('nomor_kamar', 'like', '%'.$search.'%');
            });
        }

        if ($request->string('harga')->toString() === 'termurah') {
            $query->orderBy('harga');
        } elseif ($request->string('harga')->toString() === 'termahal') {
            $query->orderByDesc('harga');
        }

        return view('rooms.index', [
            'kamars' => $query->orderBy('nomor_kamar')->paginate(12)->withQueryString(),
        ]);
    }

    public function show(Kamar $kamar): View
    {
        return view('rooms.show', compact('kamar'));
    }
}
