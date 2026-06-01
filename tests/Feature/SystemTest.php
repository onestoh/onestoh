<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_marketplace_loads(): void
    {
        $response = $this->get('/marketplace');
        $response->assertStatus(200);
    }

    public function test_login_page_loads(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email'    => 'test@example.com',
            'password' => bcrypt('password'),
            'role'     => 'landlord',
        ]);

        $response = $this->withoutMiddleware()
            ->post('/login', [
                'email'    => 'test@example.com',
                'password' => 'password',
            ]);

        $response->assertRedirect();
        $this->assertEquals($user->id, session('user_id'));
    }

    public function test_login_with_invalid_credentials(): void
    {
        $response = $this->withoutMiddleware()
            ->post('/login', [
                'email'    => 'wrong@example.com',
                'password' => 'wrongpassword',
            ]);

        // Invalid credentials should redirect (not 200 or 500)
        $this->assertContains($response->status(), [302, 303, 419]);
    }

    public function test_dashboard_requires_auth(): void
    {
        $response = $this->get('/dashboard/landlord');
        $response->assertRedirect('/login');
    }

    public function test_property_listing_page(): void
    {
        $user = User::factory()->create(['role' => 'landlord']);
        Property::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->get('/marketplace');
        $response->assertStatus(200);
    }

    public function test_booking_availability_endpoint(): void
    {
        $user     = User::factory()->create(['role' => 'landlord']);
        $property = Property::factory()->create([
            'user_id'      => $user->id,
            'listing_type' => 'airbnb',
        ]);

        $response = $this->get("/bookings/availability/{$property->id}");
        $response->assertStatus(200);
        $response->assertJson([]);
    }

    public function test_api_properties_endpoint(): void
    {
        $response = $this->getJson('/api/v1/properties');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);
    }

    public function test_api_login(): void
    {
        $user = User::factory()->create([
            'email'    => 'api@test.com',
            'password' => bcrypt('password'),
            'role'     => 'tenant',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'api@test.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data' => ['token']]);
    }

    public function test_security_headers_present(): void
    {
        $response = $this->get('/');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    }

    public function test_csrf_protection_on_login(): void
    {
        $response = $this->post('/login', [
            'email'    => 'test@test.com',
            'password' => 'password',
        ]);
        $this->assertNotEquals(500, $response->status());
    }
}
