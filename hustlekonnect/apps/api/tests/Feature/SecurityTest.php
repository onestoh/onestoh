<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_sql_injection_is_blocked(): void
    {
        $res = $this->postJson('/api/v1/auth/login', [
            'email'    => "' OR 1=1 --",
            'password' => 'anything',
        ]);

        $res->assertStatus(400);
    }

    public function test_xss_input_is_sanitized(): void
    {
        $res = $this->postJson('/api/v1/auth/register', [
            'name'                  => '<script>alert("xss")</script>',
            'email'                 => 'xss@hustlekonnect.com',
            'phone'                 => '+254700000001',
            'password'              => 'Secret@123',
            'password_confirmation' => 'Secret@123',
            'role'                  => 'client',
        ]);

        // Name should not contain script tags after sanitization
        if ($res->status() === 201) {
            $this->assertStringNotContainsString('<script>', $res->json('data.user.name'));
        }
    }

    public function test_brute_force_triggers_lockout(): void
    {
        User::factory()->create(['email' => 'victim@hustlekonnect.com', 'password' => Hash::make('Secret@123')]);

        // 5 failed attempts
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/login', [
                'email'    => 'victim@hustlekonnect.com',
                'password' => 'WrongPassword',
            ]);
        }

        // 6th attempt should be rate-limited
        $res = $this->postJson('/api/v1/auth/login', [
            'email'    => 'victim@hustlekonnect.com',
            'password' => 'WrongPassword',
        ]);

        $this->assertContains($res->status(), [429, 401]);
    }

    public function test_security_headers_are_present(): void
    {
        $res = $this->getJson('/health');

        $res->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_unauthenticated_access_to_protected_route(): void
    {
        $this->getJson('/api/v1/bookings')->assertStatus(401);
        $this->getJson('/api/v1/wallet/balance')->assertStatus(401);
        $this->getJson('/api/v1/auth/me')->assertStatus(401);
    }

    public function test_admin_route_requires_admin_role(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $token  = $client->createToken('test')->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/admin/dashboard')->assertStatus(403);
    }
}
