<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GoogleIdTokenVerifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class GoogleController extends Controller
{
    public function credential(Request $request, GoogleIdTokenVerifier $verifier): RedirectResponse
    {
        if (! filled(config('services.google.client_id'))) {
            return redirect()
                ->route('login')
                ->with('status', 'Login Google belum dikonfigurasi. Isi GOOGLE_CLIENT_ID di file .env.');
        }

        try {
            $validated = $request->validate([
                'credential' => ['required', 'string', 'max:10000'],
            ]);

            $claims = $verifier->verify($validated['credential']);
        } catch (Throwable $exception) {
            Log::warning('Google ID token verification failed.', [
                'exception' => $exception::class,
                'reason' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('login')
                ->with('status', 'Google tidak dapat memverifikasi login. Periksa Client ID dan Authorized JavaScript origins di Google Cloud Console, lalu coba lagi.');
        }

        $user = User::firstOrCreate(
            ['email' => $claims->email],
            [
                'nama' => is_string($claims->name ?? null) && filled($claims->name)
                    ? $claims->name
                    : Str::before($claims->email, '@'),
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
