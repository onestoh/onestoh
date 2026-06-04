<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Yard;
use App\Models\Asset;
use App\Models\Booking;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    private User  $client;
    private Asset $asset;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $owner = User::factory()->create(['role' => 'owner']);
        $yard  = Yard::factory()->create(['user_id' => $owner->id]);
        $this->asset = Asset::factory()->create([
            'yard_id'    => $yard->id,
            'daily_rate' => 5000,
            'status'     => 'available',
            'is_listed'  => true,
        ]);

        $this->client = User::factory()->create(['role' => 'client', 'kyc_status' => 'approved']);
        Wallet::factory()->create(['user_id' => $this->client->id, 'balance' => 100000]);
        $this->token  = $this->client->createToken('test')->plainTextToken;
    }

    public function test_client_can_create_booking(): void
    {
        $res = $this->withToken($this->token)->postJson('/api/v1/bookings', [
            'asset_id'   => $this->asset->id,
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date'   => now()->addDays(5)->toDateString(),
            'currency'   => 'KES',
        ]);

        $res->assertStatus(201)
            ->assertJsonPath('data.user_id', $this->client->id)
            ->assertJsonPath('data.status', 'pending_payment');
    }

    public function test_booking_fails_for_unavailable_asset(): void
    {
        // Create conflicting booking
        Booking::factory()->create([
            'asset_id'   => $this->asset->id,
            'start_date' => now()->addDays(1)->toDateString(),
            'end_date'   => now()->addDays(6)->toDateString(),
            'status'     => 'confirmed',
        ]);

        $res = $this->withToken($this->token)->postJson('/api/v1/bookings', [
            'asset_id'   => $this->asset->id,
            'start_date' => now()->addDays(3)->toDateString(),
            'end_date'   => now()->addDays(5)->toDateString(),
        ]);

        $res->assertStatus(422);
    }

    public function test_unverified_kyc_cannot_book(): void
    {
        $unverified = User::factory()->create(['kyc_status' => 'not_submitted']);
        $token      = $unverified->createToken('test')->plainTextToken;

        $res = $this->withToken($token)->postJson('/api/v1/bookings', [
            'asset_id'   => $this->asset->id,
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date'   => now()->addDays(4)->toDateString(),
        ]);

        $res->assertStatus(403);
    }

    public function test_client_can_list_own_bookings(): void
    {
        Booking::factory(3)->create(['user_id' => $this->client->id, 'asset_id' => $this->asset->id]);

        $res = $this->withToken($this->token)->getJson('/api/v1/bookings');
        $res->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_client_can_cancel_pending_booking(): void
    {
        $booking = Booking::factory()->create([
            'user_id'  => $this->client->id,
            'asset_id' => $this->asset->id,
            'status'   => 'pending_payment',
        ]);

        $res = $this->withToken($this->token)->postJson("/api/v1/bookings/{$booking->id}/cancel", [
            'reason' => 'Change of plans',
        ]);

        $res->assertOk();
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'cancelled']);
    }

    public function test_cannot_cancel_completed_booking(): void
    {
        $booking = Booking::factory()->create([
            'user_id'  => $this->client->id,
            'asset_id' => $this->asset->id,
            'status'   => 'completed',
        ]);

        $res = $this->withToken($this->token)->postJson("/api/v1/bookings/{$booking->id}/cancel");
        $res->assertStatus(422);
    }

    public function test_unauthenticated_cannot_book(): void
    {
        $res = $this->postJson('/api/v1/bookings', [
            'asset_id'   => $this->asset->id,
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date'   => now()->addDays(5)->toDateString(),
        ]);

        $res->assertStatus(401);
    }
}
