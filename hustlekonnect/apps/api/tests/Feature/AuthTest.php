<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $res = $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Test User',
            'email'                 => 'test@hustlekonnect.com',
            'phone'                 => '+254700000001',
            'password'              => 'Secret@123',
            'password_confirmation' => 'Secret@123',
            'role'                  => 'client',
            'country'               => 'KE',
        ]);

        $res->assertStatus(201)
            ->assertJsonPath('data.user.email', 'test@hustlekonnect.com')
            ->assertJsonPath('data.user.role', 'client')
            ->assertJsonStructure(['data' => ['user', 'token']]);

        $this->assertDatabaseHas('users', ['email' => 'test@hustlekonnect.com']);
        $this->assertDatabaseHas('wallets', ['user_id' => $res->json('data.user.id')]);
    }

    public function test_registration_rejects_weak_password(): void
    {
        $res = $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Test User',
            'email'                 => 'weak@hustlekonnect.com',
            'phone'                 => '+254700000002',
            'password'              => 'password',
            'password_confirmation' => 'password',
            'role'                  => 'client',
        ]);

        $res->assertStatus(422);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email'    => 'login@hustlekonnect.com',
            'password' => Hash::make('Secret@123'),
            'is_active'=> true,
        ]);

        $res = $this->postJson('/api/v1/auth/login', [
            'email'    => 'login@hustlekonnect.com',
            'password' => 'Secret@123',
        ]);

        $res->assertOk()
            ->assertJsonStructure(['data' => ['user', 'token']]);
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'email'     => 'inactive@hustlekonnect.com',
            'password'  => Hash::make('Secret@123'),
            'is_active' => false,
        ]);

        $res = $this->postJson('/api/v1/auth/login', [
            'email'    => 'inactive@hustlekonnect.com',
            'password' => 'Secret@123',
        ]);

        $res->assertStatus(403);
    }

    public function test_login_returns_401_for_wrong_credentials(): void
    {
        User::factory()->create(['email' => 'valid@hustlekonnect.com', 'password' => Hash::make('Secret@123')]);

        $res = $this->postJson('/api/v1/auth/login', [
            'email'    => 'valid@hustlekonnect.com',
            'password' => 'WrongPassword',
        ]);

        $res->assertStatus(401);
    }

    public function test_authenticated_user_can_get_profile(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $res = $this->withToken($token)->getJson('/api/v1/auth/me');
        $res->assertOk()->assertJsonPath('data.id', $user->id);
    }

    public function test_user_can_logout(): void
    {
        $user  = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $res = $this->withToken($token)->postJson('/api/v1/auth/logout');
        $res->assertOk();

        $this->withToken($token)->getJson('/api/v1/auth/me')->assertStatus(401);
    }

    public function test_duplicate_email_registration_fails(): void
    {
        User::factory()->create(['email' => 'dup@hustlekonnect.com']);

        $res = $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Dup User',
            'email'                 => 'dup@hustlekonnect.com',
            'phone'                 => '+254700000099',
            'password'              => 'Secret@123',
            'password_confirmation' => 'Secret@123',
            'role'                  => 'client',
        ]);

        $res->assertStatus(422)->assertJsonValidationErrors(['email']);
    }
}
