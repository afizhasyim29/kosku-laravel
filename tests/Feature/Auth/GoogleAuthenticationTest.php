<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_has_google_sign_in_link(): void
    {
        $this->configureGoogle();

        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('google.redirect', absolute: false), false)
            ->assertSee('Lanjutkan dengan Google');
    }

    public function test_google_redirect_starts_socialite_oauth_flow(): void
    {
        $this->configureGoogle();

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('redirect')
            ->once()
            ->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));
        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

        $this->get(route('google.redirect'))
            ->assertRedirect('https://accounts.google.com/o/oauth2/auth');
    }

    public function test_verified_google_user_can_sign_in_and_is_created(): void
    {
        $this->configureGoogle();

        $googleUser = SocialiteUser::fake([
            'name' => 'Google Test User',
            'email' => 'google@example.com',
        ]);
        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->once()->andReturn($googleUser);
        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

        $this->get(route('google.callback'))
            ->assertRedirect(route('booking.create', absolute: false));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'nama' => 'Google Test User',
            'email' => 'google@example.com',
        ]);
        $this->assertNotNull(User::where('email', 'google@example.com')->firstOrFail()->email_verified_at);
    }

    public function test_google_login_shows_a_configuration_message_when_redirect_uri_is_missing(): void
    {
        $this->configureGoogle();
        config(['services.google.redirect' => '']);

        $this->get(route('google.redirect'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('status', fn (string $message): bool => str_contains($message, 'GOOGLE_REDIRECT_URI'));
    }

    public function test_google_login_rejects_a_profile_without_an_email(): void
    {
        $this->configureGoogle();

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')
            ->once()
            ->andReturn(SocialiteUser::fake(['email' => null]));
        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

        $this->get(route('google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('status');

        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    private function configureGoogle(): void
    {
        config([
            'services.google.client_id' => 'test-client-id',
            'services.google.client_secret' => 'test-client-secret',
            'services.google.redirect' => 'https://example.test/auth/google/callback',
        ]);
    }
}
