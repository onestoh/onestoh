<?php
namespace Tests\Feature;

use App\Models\Asset;
use App\Models\Booking;
use App\Models\EscrowAccount;
use App\Models\Payment;
use App\Models\User;
use App\Models\Yard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $client;
    private User $owner;
    private Asset $asset;
    private string $clientToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create(['role' => 'owner', 'kyc_status' => 'approved']);
        $this->client = User::factory()->create(['role' => 'client', 'kyc_status' => 'approved']);

        $yard = Yard::factory()->create(['user_id' => $this->owner->id]);
        $this->asset = Asset::factory()->create([
            'yard_id' => $yard->id,
            'daily_rate_kes' => 5000,
            'is_active' => true,
            'is_available' => true,
        ]);

        $this->clientToken = $this->client->createToken('test')->plainTextToken;
    }

    public function test_client_can_create_booking(): void
    {
        $response = $this->withHeaders(['Authorization' => "Bearer {$this->clientToken}"])
            ->postJson('/api/v1/bookings', [
                'asset_id' => $this->asset->id,
                'start_date' => now()->addDays(3)->toDateString(),
                'end_date' => now()->addDays(6)->toDateString(),
                'insurance_type' => 'cdw',
            ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['data' => ['id', 'status', 'total_amount_kes']]);
        $this->assertEquals('pending_payment', $response->json('data.status'));
    }

    public function test_booking_auto_confirms_after_payment(): void
    {
        $booking = Booking::factory()->create([
            'user_id' => $this->client->id,
            'asset_id' => $this->asset->id,
            'status' => 'payment_processing',
        ]);

        $payment = Payment::factory()->create([
            'booking_id' => $booking->id,
            'user_id' => $this->client->id,
            'amount' => 15000,
            'status' => 'processing',
        ]);

        // Simulate payment completion
        $payment->update(['status' => 'completed']);

        $booking->refresh();
        $this->assertEquals('confirmed', $booking->status);
    }

    public function test_escrow_is_created_on_payment_confirmation(): void
    {
        $booking = Booking::factory()->create([
            'user_id' => $this->client->id,
            'asset_id' => $this->asset->id,
            'status' => 'payment_processing',
        ]);

        $payment = Payment::factory()->create([
            'booking_id' => $booking->id,
            'user_id' => $this->client->id,
            'amount' => 15000,
            'status' => 'processing',
        ]);

        $payment->update(['status' => 'completed']);

        $this->assertDatabaseHas('escrow_accounts', [
            'booking_id' => $booking->id,
            'status' => 'held',
        ]);
    }

    public function test_booking_cannot_overlap_with_existing_confirmed_booking(): void
    {
        // Create existing confirmed booking
        Booking::factory()->create([
            'asset_id' => $this->asset->id,
            'status' => 'confirmed',
            'start_date' => now()->addDays(2)->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
        ]);

        // Try to create overlapping booking
        $response = $this->withHeaders(['Authorization' => "Bearer {$this->clientToken}"])
            ->postJson('/api/v1/bookings', [
                'asset_id' => $this->asset->id,
                'start_date' => now()->addDays(4)->toDateString(),
                'end_date' => now()->addDays(9)->toDateString(),
            ]);

        $response->assertStatus(422);
    }

    public function test_booking_requires_kyc_approved_user(): void
    {
        $unverifiedClient = User::factory()->create(['role' => 'client', 'kyc_status' => 'pending']);
        $token = $unverifiedClient->createToken('test')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->postJson('/api/v1/bookings', [
                'asset_id' => $this->asset->id,
                'start_date' => now()->addDays(3)->toDateString(),
                'end_date' => now()->addDays(6)->toDateString(),
            ]);

        $response->assertStatus(403);
    }

    public function test_owner_receives_notification_on_booking_confirmation(): void
    {
        $booking = Booking::factory()->create([
            'user_id' => $this->client->id,
            'asset_id' => $this->asset->id,
            'status' => 'pending_payment',
        ]);

        $booking->update(['status' => 'confirmed']);

        // Check notification was queued for owner
        $this->assertDatabaseHas('platform_notifications', [
            'user_id' => $this->owner->id,
        ]);
    }

    public function test_full_booking_lifecycle(): void
    {
        $booking = Booking::factory()->create([
            'user_id' => $this->client->id,
            'asset_id' => $this->asset->id,
            'status' => 'pending_payment',
            'start_date' => now()->subHours(2)->toDateTimeString(),
            'end_date' => now()->addDays(2)->toDateTimeString(),
        ]);

        $stages = ['confirmed', 'owner_notified', 'client_prepared', 'active', 'completed'];

        foreach ($stages as $stage) {
            $booking->update(['status' => $stage]);
            $booking->refresh();
            $this->assertEquals($stage, $booking->status);
        }
    }
}
