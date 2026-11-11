<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Pesanan;
use App\Models\User;
use Database\Seeders\KamarSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_kamar_seeder_creates_available_rooms_idempotently(): void
    {
        $seeder = new KamarSeeder;
        $seeder->run();
        $seeder->run();

        $this->assertDatabaseCount('kamars', 5);
        $this->assertSame(
            5,
            Kamar::query()->where('status', 'tersedia')->count()
        );
    }

    public function test_landing_page_booking_button_opens_booking_after_login(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(route('booking.create'), false);

        $this->get(route('booking.create'))
            ->assertRedirect(route('login'));
    }

    public function test_login_returns_the_user_to_the_booking_form_they_requested(): void
    {
        $user = User::factory()->create();

        $this->get(route('booking.create'))
            ->assertRedirect(route('login'));

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('booking.create', absolute: false));
    }

    public function test_booking_page_shows_available_rooms_from_database(): void
    {
        $user = User::factory()->create();
        Kamar::query()->create([
            'tipe_kamar' => 'Deluxe',
            'nomor_kamar' => '101',
            'fasilitas' => 'AC, WiFi',
            'harga' => 1000000,
            'status' => 'tersedia',
        ]);
        Kamar::query()->create([
            'tipe_kamar' => 'Standard',
            'nomor_kamar' => '102',
            'harga' => 750000,
            'status' => 'terisi',
        ]);

        $this->actingAs($user)
            ->get(route('booking.create'))
            ->assertOk()
            ->assertSee('Deluxe')
            ->assertSee('101')
            ->assertDontSee('102');
    }

    public function test_booking_submission_creates_a_pending_order_with_server_calculated_discount(): void
    {
        $user = User::factory()->create();
        $kamar = Kamar::query()->create([
            'tipe_kamar' => 'Deluxe',
            'nomor_kamar' => '201',
            'harga' => 1000000,
            'status' => 'tersedia',
        ]);

        $this->actingAs($user)
            ->post(route('booking.store'), [
                'kamar_id' => $kamar->id,
                'tanggal_masuk' => now()->addDay()->toDateString(),
                'durasi_bulan' => 3,
                'total' => 1,
            ])
            ->assertRedirect(route('payments.booking', Pesanan::query()->latest('id')->firstOrFail()));

        $this->assertDatabaseHas('pesanans', [
            'user_id' => $user->id,
            'kamar_id' => $kamar->id,
            'durasi_bulan' => 3,
            'total_bayar' => 2850000,
            'status_pembayaran' => 'menunggu',
        ]);
        $this->assertDatabaseHas('kamars', ['id' => $kamar->id, 'status' => 'dipesan']);
    }

    public function test_booking_submission_rejects_a_room_that_is_no_longer_available(): void
    {
        $user = User::factory()->create();
        $kamar = Kamar::query()->create([
            'tipe_kamar' => 'Standard',
            'nomor_kamar' => '301',
            'harga' => 750000,
            'status' => 'terisi',
        ]);

        $this->actingAs($user)
            ->from(route('booking.create'))
            ->post(route('booking.store'), [
                'kamar_id' => $kamar->id,
                'tanggal_masuk' => now()->addDay()->toDateString(),
                'durasi_bulan' => 1,
            ])
            ->assertRedirect(route('booking.create'))
            ->assertSessionHasErrors('kamar_id');

        $this->assertSame(0, Pesanan::query()->count());
    }
}
