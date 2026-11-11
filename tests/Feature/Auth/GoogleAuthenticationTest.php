<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GoogleAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_has_google_sign_in_button(): void
    {
        config(['services.google.client_id' => 'test-client-id']);

        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('google.callback', absolute: false), false)
            ->assertSee('continue_with');
    }

    public function test_verified_google_user_can_sign_in_and_is_created(): void
    {
        config(['services.google.client_id' => 'test-client-id']);
        $privateKey = $this->fakeGoogleSigningKeys();
        $credential = $this->googleIdToken($privateKey);

        $this->post(route('google.callback'), ['credential' => $credential])
            ->assertRedirect(route('booking.create', absolute: false));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'nama' => 'Google Test User',
            'email' => 'google@example.com',
        ]);
        $this->assertNotNull(User::where('email', 'google@example.com')->firstOrFail()->email_verified_at);
    }

    public function test_google_login_shows_a_configuration_message_when_client_id_is_missing(): void
    {
        config(['services.google.client_id' => '']);

        $this->post(route('google.callback'), ['credential' => 'not-a-token'])
            ->assertRedirect(route('login'))
            ->assertSessionHas('status', fn (string $message): bool => str_contains($message, 'GOOGLE_CLIENT_ID'));
    }

    public function test_google_id_token_with_wrong_audience_is_rejected(): void
    {
        config(['services.google.client_id' => 'test-client-id']);
        $privateKey = $this->fakeGoogleSigningKeys();
        $credential = $this->googleIdToken($privateKey, ['aud' => 'another-client-id']);

        $this->from(route('login'))
            ->post(route('google.callback'), ['credential' => $credential])
            ->assertRedirect(route('login'))
            ->assertSessionHas('status');

        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_google_id_token_with_an_untrusted_signature_is_rejected(): void
    {
        config(['services.google.client_id' => 'test-client-id']);
        $this->fakeGoogleSigningKeys();
        $credential = $this->googleIdToken($this->createGoogleSigningKey());

        $this->from(route('login'))
            ->post(route('google.callback'), ['credential' => $credential])
            ->assertRedirect(route('login'))
            ->assertSessionHas('status');

        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    private function fakeGoogleSigningKeys(): \OpenSSLAsymmetricKey
    {
        $privateKey = $this->createGoogleSigningKey();
        $details = openssl_pkey_get_details($privateKey);
        $this->assertIsArray($details);

        Http::fake([
            'https://www.googleapis.com/oauth2/v3/certs' => Http::response([
                'keys' => [[
                    'kty' => 'RSA',
                    'kid' => 'test-key',
                    'use' => 'sig',
                    'alg' => 'RS256',
                    'n' => $this->base64UrlEncode($details['rsa']['n']),
                    'e' => $this->base64UrlEncode($details['rsa']['e']),
                ]],
            ]),
        ]);

        return $privateKey;
    }

    private function createGoogleSigningKey(): \OpenSSLAsymmetricKey
    {
        $options = [
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
            'private_key_bits' => 2048,
        ];
        $opensslConfig = getenv('OPENSSL_CONF');
        if (! $opensslConfig && PHP_OS_FAMILY === 'Windows') {
            $candidate = dirname(PHP_BINARY).DIRECTORY_SEPARATOR.'extras'.DIRECTORY_SEPARATOR.'ssl'.DIRECTORY_SEPARATOR.'openssl.cnf';
            $opensslConfig = is_file($candidate) ? $candidate : null;
        }
        if ($opensslConfig) {
            $options['config'] = $opensslConfig;
        }

        $privateKey = openssl_pkey_new($options);

        $this->assertNotFalse($privateKey);

        return $privateKey;
    }

    private function googleIdToken(\OpenSSLAsymmetricKey $privateKey, array $overrides = []): string
    {
        $now = time();
        $claims = array_merge([
            'iss' => 'https://accounts.google.com',
            'aud' => 'test-client-id',
            'sub' => 'google-user-123',
            'email' => 'google@example.com',
            'email_verified' => true,
            'name' => 'Google Test User',
            'iat' => $now,
            'exp' => $now + 3600,
        ], $overrides);

        return JWT::encode($claims, $privateKey, 'RS256', 'test-key');
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
