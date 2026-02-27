<?php

namespace Tests\Feature\v1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserAuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */

       protected string $endpoint = '/v1/users/auth/token';

    public function test_user_can_login_and_receive_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(), // usuário verificado
        ]);

        $response = $this->postJson($this->endpoint, [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'access_token',
                     'token_type',
                 ])
                 ->assertJson([
                     'token_type' => 'Bearer',
                 ]);

        // Verifica que um token foi criado
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_unverified_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => null, // não verificado
        ]);

        $response = $this->postJson($this->endpoint, [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'message' => 'User is not verified, please check the email verification link',
                 ]);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson($this->endpoint, [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'message' => 'Wrong credentials',
                 ]);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_user_can_logout_and_token_is_deleted(): void
    {
        $user = User::factory()->create();

        $token = $user->createToken('test-token');

        $plainTextToken = $token->plainTextToken;

        $response = $this->withHeader(
            'Authorization',
            'Bearer ' . $plainTextToken
        )->deleteJson('/v1/users/auth/token');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logged out successfully'
            ]);

      
        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);
    }
}
