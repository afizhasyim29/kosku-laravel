<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home', [
            'kamars' => Kamar::query()
                ->whereIn('status', ['tersedia', 'kosong'])
                ->orderBy('nomor_kamar')
                ->limit(3)
                ->get(),
        ]);
    }
}
