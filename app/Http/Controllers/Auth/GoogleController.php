<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleController extends Controller
{
    public function redirect(): RedirectResponse
    {
        if (! filled(config('services.google.client_id'))
            || ! filled(config('services.google.client_secret'))
            || ! filled(config('services.google.redirect'))) {
            return redirect()
                ->route('login')
                ->with('status', 'Login Google belum dikonfigurasi. Periksa GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, dan GOOGLE_REDIRECT_URI di file .env.');
        }

        try {
            return Socialite::driver('google')->redirect();
        } catch (Throwable $exception) {
            Log::error('Google OAuth redirect failed.', [
                'exception' => $exception::class,
                'reason' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('login')
                ->with('status', 'Login Google gagal dimulai. Periksa konfigurasi OAuth Google dan coba lagi.');
        }
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $exception) {
            Log::warning('Google OAuth callback failed.', [
                'exception' => $exception::class,
                'reason' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('login')
                ->with('status', 'Login Google gagal. Pastikan Redirect URI di Google Cloud Console sama dengan GOOGLE_REDIRECT_URI, lalu coba lagi.');
        }

        $email = $googleUser->getEmail();
        if (! is_string($email) || ! filled($email)) {
            return redirect()
                ->route('login')
                ->with('status', 'Akun Google tidak memberikan alamat email. Izinkan akses email lalu coba lagi.');
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'nama' => is_string($googleUser->getName()) && filled($googleUser->getName())
                    ? $googleUser->getName()
                    : Str::before($email, '@'),
                'password' => Str::random(48),
            ]
        );

        if ($user->wasRecentlyCreated) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        Auth::login($user);
        request()->session()->regenerate();

        return redirect()->intended(route('booking.create', absolute: false));
    }
}
