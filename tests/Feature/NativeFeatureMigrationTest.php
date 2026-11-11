<?php

namespace Tests\Feature;

use App\Models\Kamar;
use App\Models\Laporan;
use App\Models\PerpanjanganPesanan;
use App\Models\Pesanan;
use App\Models\PesananLayanan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NativeFeatureMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_all_static_and_resident_pages(): void
    {
        $user = User::factory()->create();

        foreach ([
            'aturan',
            'faq',
            'fasilitas',
            'bantuan',
            'layanan',
            'rooms.index',
            'orders.index',
            'resident.card',
        ] as $route) {
            $this->actingAs($user)->get(route($route))->assertOk();
        }
    }

    public function test_catalog_filters_rooms_and_detail_shows_room_information(): void
    {
        $user = User::factory()->create();
        $room = $this->createRoom('101', 'Deluxe', 1200000);
        $this->createRoom('201', 'Standard', 800000);

        $this->actingAs($user)
            ->get(route('rooms.index', ['lantai' => '1', 'cari' => 'Deluxe']))
            ->assertOk()
            ->assertSee('101')
            ->assertDontSee('201');

        $this->get(route('rooms.show', $room))
            ->assertOk()
            ->assertSee('Deluxe')
            ->assertSee('AC dan Wi-Fi')
            ->assertSee('-6.3438,106.7386')
            ->assertSee('Bagikan Kamar');
    }

    public function test_tenant_can_submit_a_report_only_for_a_paid_room_and_order_a_service(): void
    {
        $user = User::factory()->create();
        $room = $this->createRoom('101');
        $order = $this->createOrder($user, $room, 'lunas');

        $this->actingAs($user)
            ->from(route('bantuan'))
            ->post(route('bantuan.store'), [
                'kamar_id' => $room->id,
                'keluhan' => 'Lampu kamar tidak menyala.',
            ])
            ->assertRedirect(route('bantuan'));

        $this->assertDatabaseHas('laporans', [
            'user_id' => $user->id,
            'kamar_id' => $room->id,
            'status' => 'baru',
        ]);

        $this->from(route('layanan'))
            ->post(route('layanan.store'), [
                'jenis_layanan' => 'Bersihkan kamar',
                'catatan' => 'Mohon sore hari.',
            ])
            ->assertRedirect(route('layanan'));

        $this->assertDatabaseHas('pesanan_layanans', [
            'user_id' => $user->id,
            'jenis_layanan' => 'Bersihkan kamar',
            'status' => 'baru',
        ]);

        $otherRoom = $this->createRoom('102');
        $this->from(route('bantuan'))
            ->post(route('bantuan.store'), [
                'kamar_id' => $otherRoom->id,
                'keluhan' => 'Keluhan untuk kamar yang bukan sewaan saya.',
            ])
            ->assertRedirect(route('bantuan'))
            ->assertSessionHasErrors('kamar_id');

        $this->assertSame(1, Laporan::query()->count());
        $this->assertSame(1, PesananLayanan::query()->count());
        $this->assertDatabaseHas('pesanans', ['id' => $order->id]);
    }

    public function test_simulated_payment_activates_booking_and_exposes_proofs(): void
    {
        $tenant = User::factory()->create();
        $room = $this->createRoom('101');
        $order = $this->createOrder($tenant, $room, 'menunggu');

        $this->actingAs($tenant)
            ->get(route('payments.booking', $order))
            ->assertOk()
            ->assertSee('QRIS');

        $this->post(route('payments.booking.confirm', $order))
            ->assertRedirect(route('orders.ticket', $order->kode_tiket));

        $this->assertDatabaseHas('pesanans', [
            'id' => $order->id,
            'status_pembayaran' => 'lunas',
        ]);
        $this->assertDatabaseHas('kamars', ['id' => $room->id, 'status' => 'terisi']);

        $this->actingAs($tenant)
            ->get(route('orders.ticket', $order->kode_tiket))
            ->assertOk()
            ->assertSee($order->kode_tiket)
            ->assertSee('data-qrcode="'.$order->kode_tiket.'"', false)
            ->assertSee('Tiket Penghuni');
        $this->get(route('orders.index'))
            ->assertOk()
            ->assertSee(route('orders.ticket', $order->kode_tiket), false)
            ->assertSee(route('orders.receipt', $order->kode_tiket), false);
        $this->get(route('orders.invoice', $order->kode_tiket))
            ->assertOk()
            ->assertSee($tenant->nama);
        $this->get(route('orders.receipt', $order->kode_tiket))->assertOk();
        $this->get(route('resident.card'))->assertOk()->assertSee($order->kode_tiket);
        $this->get(route('scanner.api', ['kode' => $order->kode_tiket]))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('penghuni', $tenant->nama);
        $this->get(route('scanner.kamar-masuk', ['kode' => $order->kode_tiket]))
            ->assertOk()
            ->assertSee('Silakan Masuk!');

        $this->actingAs($tenant)
            ->get(route('orders.invoice', $order->kode_tiket))
            ->assertOk()
            ->assertSee($tenant->nama);
    }

    public function test_booking_payment_cannot_be_approved_by_a_tenant(): void
    {
        $tenant = User::factory()->create();
        $room = $this->createRoom('101');
        $order = $this->createOrder($tenant, $room, 'menunggu_verifikasi');

        $this->actingAs($tenant)
            ->post(route('admin.booking-payment', $order), ['status' => 'lunas'])
            ->assertForbidden();

        $this->assertDatabaseHas('pesanans', [
            'id' => $order->id,
            'status_pembayaran' => 'menunggu_verifikasi',
        ]);
    }

    public function test_scanner_link_is_next_to_faq_and_any_logged_in_user_can_open_it(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $tenant = User::factory()->create();

        $response = $this->actingAs($admin)
            ->get(route('rooms.index'))
            ->assertOk()
            ->assertSee(route('faq'), false)
            ->assertSee(route('scanner.index'), false);

        $content = $response->getContent();
        $this->assertLessThan(strpos($content, '>Scanner</a>'), strpos($content, '>FAQ</a>'));

        $this->actingAs($tenant)
            ->get(route('scanner.index'))
            ->assertOk()
            ->assertSee('Pemindai QR');

        $this->get(route('rooms.index'))
            ->assertOk()
            ->assertSee(route('scanner.index'), false);
    }

    public function test_simulated_extension_payment_updates_term_and_order_total(): void
    {
        $tenant = User::factory()->create();
        $room = $this->createRoom('101', 'Deluxe', 1000000, 'terisi');
        $order = $this->createOrder($tenant, $room, 'lunas');

        $this->actingAs($tenant)
            ->post(route('extensions.store', $order), ['durasi_bulan' => 3])
            ->assertRedirect();

        $extension = PerpanjanganPesanan::query()->firstOrFail();
        $this->assertSame('menunggu_pembayaran', $extension->status_pembayaran);
        $this->assertSame(1, $order->fresh()->durasi_bulan);

        $this->get(route('extensions.payment', $extension))->assertOk()->assertSee('QRIS');
        $this->post(route('extensions.payment.confirm', $extension))
            ->assertRedirect(route('orders.index'));

        $this->assertSame(4, $order->fresh()->durasi_bulan);
        $this->assertSame(4000000.0, (float) $order->fresh()->total_bayar);
        $this->assertSame('lunas', $extension->fresh()->status_pembayaran);
        $this->get(route('orders.index'))
            ->assertOk()
            ->assertSee('Riwayat perpanjangan')
            ->assertSee('3 bulan');
        $this->get(route('orders.receipt', $order->kode_tiket))->assertOk();
    }

    public function test_admin_can_close_reports_and_validate_paid_ticket(): void
    {
        $tenant = User::factory()->create();
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $room = $this->createRoom('101', 'Deluxe', 1000000, 'terisi');
        $order = $this->createOrder($tenant, $room, 'lunas');
        $report = Laporan::query()->create([
            'user_id' => $tenant->id,
            'kamar_id' => $room->id,
            'keluhan' => 'Perlu diperiksa.',
            'status' => 'baru',
            'tanggal_lapor' => now(),
        ]);
        $service = PesananLayanan::query()->create([
            'user_id' => $tenant->id,
            'jenis_layanan' => 'Air mineral',
            'status' => 'baru',
            'tanggal_pesan' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.index'))
            ->assertOk()
            ->assertSee($report->keluhan);
        $this->get(route('scanner.index'))
            ->assertOk()
            ->assertSee('Pemindai QR')
            ->assertSee('Arahkan kamera ke QR');
        $this->post(route('admin.report.close', $report))->assertRedirect();
        $this->assertSame('selesai', $report->fresh()->status);
        $this->post(route('admin.service.close', $service))->assertRedirect();
        $this->assertSame('selesai', $service->fresh()->status);

        $this->get(route('scanner.api', ['kode' => $order->kode_tiket]))
            ->assertOk()
            ->assertJsonPath('penghuni', $tenant->nama);
        $this->get(route('scanner.kamar-masuk', ['kode' => $order->kode_tiket]))
            ->assertOk()
            ->assertSee($tenant->nama)
            ->assertSee('Pembayaran Lunas')
            ->assertSee('Silakan Masuk!')
            ->assertSee('Scan Tiket Berikutnya');

        $pending = $this->createOrder($tenant, $this->createRoom('102'), 'menunggu');
        $this->get(route('scanner.api', ['kode' => $pending->kode_tiket]))
            ->assertNotFound()
            ->assertJsonPath('success', false);
    }

    private function createRoom(
        string $number,
        string $type = 'Standard',
        float $price = 750000,
        string $status = 'tersedia',
    ): Kamar {
        return Kamar::query()->create([
            'tipe_kamar' => $type,
            'nomor_kamar' => $number,
            'fasilitas' => 'AC dan Wi-Fi',
            'harga' => $price,
            'status' => $status,
        ]);
    }

    private function createOrder(User $user, Kamar $room, string $status): Pesanan
    {
        if (in_array($status, ['menunggu', 'menunggu_verifikasi'], true)) {
            $room->update(['status' => 'dipesan']);
        } elseif ($status === 'lunas') {
            $room->update(['status' => 'terisi']);
        }

        return Pesanan::query()->create([
            'user_id' => $user->id,
            'kamar_id' => $room->id,
            'tanggal_masuk' => now()->toDateString(),
            'durasi_bulan' => 1,
            'total_bayar' => (float) $room->harga,
            'status_pembayaran' => $status,
            'kode_tiket' => 'TEST-'.strtoupper(str()->random(10)),
            'paid_at' => $status === 'lunas' ? now() : null,
        ]);
    }
}
