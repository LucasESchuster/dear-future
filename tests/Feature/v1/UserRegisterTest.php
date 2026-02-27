<?php

namespace Tests\Feature\v1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Tests\TestCase;

class UserRegisterTest extends TestCase
{
    use RefreshDatabase;

    protected string $endpoint = '/v1/users';

    public function test_user_can_register_and_receive_token(): void
    {
        Notification::fake();

        $response = $this->postJson($this->endpoint, [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'data' => [
                         'id',
                         'name',
                         'email',
                         'created_at',
                         'updated_at',
                     ],
                     'access_token',
                 ])
                 ->assertJson([
                     'message' => 'User created successfully.',
                 ]);
        $this->assertDatabaseHas('users', [
            'email' => 'johndoe@example.com',
        ]);

        $user = User::where('email', 'johndoe@example.com')->first();

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class,
        ]);

        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );
    }
}