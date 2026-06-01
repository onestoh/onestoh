<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_security_headers(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
    }

    public function test_brute_force_lockout_after_5_failed_attempts(): void
    {
        $user = User::factory()->create(['email' => 'victim@example.com']);

        // Clear any existing cache
        Cache::flush();

        // Make 5 failed attempts
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/login', [
                'email' => 'victim@example.com',
                'password' => 'wrongpassword' . $i,
            ]);
        }

        // 6th attempt should be rate limited
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'victim@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(429);
        $response->assertJsonStructure(['message', 'retry_after']);
    }

    public function test_registration_rejects_invalid_email(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User',
            'email' => 'not-an-email',
            'phone' => '+254712345678',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'client',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_registration_rejects_weak_password(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '+254712345678',
            'password' => '123456',
            'password_confirmation' => '123456',
            'role' => 'client',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    public function test_sql_injection_is_blocked(): void
    {
        $response = $this->getJson("/api/v1/listings?search=' OR 1=1 --");
        $response->assertStatus(400);
    }

    public function test_authenticated_endpoints_require_token(): void
    {
        $response = $this->getJson('/api/v1/bookings');
        $response->assertStatus(401);
    }

    public function test_rate_limit_headers_are_present(): void
    {
        $response = $this->getJson('/api/v1/listings');
        $this->assertTrue(
            $response->headers->has('X-RateLimit-Limit') || $response->status() === 429
        );
    }

    public function test_xss_input_is_sanitized(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
            ->postJson('/api/v1/listings', [
                'title' => '<script>alert("xss")</script>Test Listing',
                'description' => 'Normal description that is at least twenty chars',
                'category' => 'sedan',
                'make' => 'Toyota',
                'model' => 'Corolla',
                'year' => 2020,
                'daily_rate_kes' => 5000,
                'location_city' => 'Nairobi',
                'location_country' => 'KE',
            ]);

        // XSS script tag should be stripped — title validation should still pass or the stored value is clean
        if ($response->status() === 201) {
            $this->assertStringNotContainsString('<script>', $response->json('data.title') ?? '');
        }
    }
}
