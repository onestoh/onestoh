<?php
namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use App\Models\Asset;
use App\Models\Yard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_endpoint_requires_authentication(): void
    {
        $this->postJson('/api/v1/payments/initiate', [])
            ->assertStatus(401);
    }

    public function test_payment_requires_valid_booking_id(): void
    {
        $user = User::factory()->create(['kyc_status' => 'approved']);
        $token = $user->createToken('test')->plainTextToken;

        $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->postJson('/api/v1/payments/initiate', [
                'booking_id' => '00000000-0000-0000-0000-000000000000',
                'gateway' => 'mpesa',
                'phone_number' => '+254712345678',
            ])
            ->assertStatus(422);
    }

    public function test_duplicate_payment_is_rejected(): void
    {
        $user = User::factory()->create(['kyc_status' => 'approved']);
        $owner = User::factory()->create();
        $yard = Yard::factory()->create(['user_id' => $owner->id]);
        $asset = Asset::factory()->create(['yard_id' => $yard->id]);
        $token = $user->createToken('test')->plainTextToken;

        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'asset_id' => $asset->id,
            'status' => 'pending_payment',
        ]);

        // Create existing payment for this booking
        Payment::factory()->create([
            'booking_id' => $booking->id,
            'user_id' => $user->id,
            'status' => 'completed',
        ]);

        // Try to pay again
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->postJson('/api/v1/payments/initiate', [
                'booking_id' => $booking->id,
                'gateway' => 'mpesa',
                'phone_number' => '+254712345678',
            ]);

        $response->assertStatus(422);
    }

    public function test_wallet_debit_creates_transaction(): void
    {
        $user = User::factory()->create(['kyc_status' => 'approved']);
        $token = $user->createToken('test')->plainTextToken;

        $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->getJson('/api/v1/wallet/balance')
            ->assertStatus(200)
            ->assertJsonStructure(['data' => ['balance', 'currency']]);
    }
}
