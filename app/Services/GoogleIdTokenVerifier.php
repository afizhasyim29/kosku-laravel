<?php

namespace App\Services;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use UnexpectedValueException;

class GoogleIdTokenVerifier
{
    public function verify(string $idToken): \stdClass
    {
        $clientId = config('services.google.client_id');

        if (! filled($clientId)) {
            throw new UnexpectedValueException('Google Client ID is not configured.');
        }

        $keySet = Cache::remember('google-id-token-jwks', now()->addHour(), function (): array {
            $keySet = Http::acceptJson()
                ->connectTimeout(3)
                ->timeout(5)
                ->get('https://www.googleapis.com/oauth2/v3/certs')
                ->throw()
                ->json();

            if (! is_array($keySet) || empty($keySet['keys'])) {
                throw new UnexpectedValueException('Google did not return a valid signing key set.');
            }

            return $keySet;
        });

        $claims = JWT::decode($idToken, JWK::parseKeySet($keySet, 'RS256'));

        $validIssuer = in_array($claims->iss ?? null, [
            'https://accounts.google.com',
            'accounts.google.com',
        ], true);
        $audience = $claims->aud ?? null;
        $validAudience = is_string($audience)
            ? hash_equals($clientId, $audience)
            : (is_array($audience) && in_array($clientId, $audience, true));

        if (! $validIssuer || ! $validAudience) {
            throw new UnexpectedValueException('Google ID token issuer or audience is invalid.');
        }

        if (
            ! is_int($claims->iat ?? null)
            || ! is_int($claims->exp ?? null)
            || $claims->iat > time() + 60
        ) {
            throw new UnexpectedValueException('Google ID token timestamps are invalid.');
        }

        if (
            ! is_string($claims->sub ?? null)
            || $claims->sub === ''
            || ! is_string($claims->email ?? null)
            || ! filter_var($claims->email, FILTER_VALIDATE_EMAIL)
            || ($claims->email_verified ?? false) !== true
        ) {
            throw new UnexpectedValueException('Google ID token does not contain a verified email.');
        }

        return $claims;
    }
}
