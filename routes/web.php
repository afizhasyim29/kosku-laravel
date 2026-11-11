<?php

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KoskuPageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ScanController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::post('/auth/google/callback', [GoogleController::class, 'credential'])
        ->middleware('throttle:10,1')
        ->name('google.callback');
});

Route::middleware('auth')->group(function () {
    Route::get('/booking', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');

    Route::get('/katalog', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('/kamar/{kamar}', [RoomController::class, 'show'])->name('rooms.show');
    Route::get('/checkout/{kamar}', fn ($kamar) => redirect()->route('booking.create', ['kamar_id' => $kamar]))
        ->name('checkout.show');

    Route::view('/aturan', 'pages.aturan')->name('aturan');
    Route::get('/bantuan', [KoskuPageController::class, 'bantuan'])->name('bantuan');
    Route::post('/bantuan', [KoskuPageController::class, 'storeLaporan'])->name('bantuan.store');
    Route::view('/faq', 'pages.faq')->name('faq');
    Route::view('/fasilitas', 'pages.fasilitas')->name('fasilitas');
    Route::get('/layanan', [KoskuPageController::class, 'layanan'])->name('layanan');
    Route::post('/layanan', [KoskuPageController::class, 'storeLayanan'])->name('layanan.store');

    Route::get('/pesanan', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/invoice/{kode}', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('/tiket/{kode}', [OrderController::class, 'ticket'])->name('orders.ticket');
    Route::get('/kuitansi/{kode}', [OrderController::class, 'kuitansi'])->name('orders.receipt');
    Route::get('/kartu-penghuni', [OrderController::class, 'kartuPenghuni'])->name('resident.card');
    Route::get('/pesanan/{pesanan}/perpanjang', [OrderController::class, 'extend'])->name('orders.extend');
    Route::post('/pesanan/{pesanan}/perpanjang', [OrderController::class, 'storeExtension'])->name('extensions.store');

    Route::get('/pesanan/{pesanan}/pembayaran', [PaymentController::class, 'booking'])->name('payments.booking');
    Route::post('/pesanan/{pesanan}/pembayaran', [PaymentController::class, 'confirmBooking'])->name('payments.booking.confirm');
    Route::get('/perpanjangan/{perpanjangan}/pembayaran', [PaymentController::class, 'extension'])->name('extensions.payment');
    Route::post('/perpanjangan/{perpanjangan}/pembayaran', [PaymentController::class, 'confirmExtension'])->name('extensions.payment.confirm');

    Route::get('/pemindai', [ScanController::class, 'index'])->name('scanner.index');
    Route::get('/api/scan', [ScanController::class, 'scan'])->name('scanner.api');
    Route::get('/kamar-masuk', [ScanController::class, 'kamarMasuk'])->name('scanner.kamar-masuk');

    Route::get('/pengelola/laporan', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/pengelola/pesanan/{pesanan}/pembayaran', [AdminController::class, 'bookingPayment'])->name('admin.booking-payment');
    Route::post('/pengelola/perpanjangan/{perpanjangan}/pembayaran', [AdminController::class, 'extensionPayment'])->name('admin.extension-payment');
    Route::post('/pengelola/laporan/{laporan}/selesai', [AdminController::class, 'closeReport'])->name('admin.report.close');
    Route::post('/pengelola/layanan/{pesananLayanan}/selesai', [AdminController::class, 'closeService'])->name('admin.service.close');
});

Route::get('/dashboard', function () {
    return redirect()->route('rooms.index');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
