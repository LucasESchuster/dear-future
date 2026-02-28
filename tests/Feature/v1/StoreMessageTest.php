<?php

namespace Tests\Feature\v1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StoreMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_message_successfully()
    {
        $user = User::factory()->create();

        $payload = [
            'title' => 'Test Title',
            'content' => 'Test Content',
            'should_notify_at' => now()->addDay()->toDateTimeString(),
            'emails' => [
                'johndoe1@email.com',
                'johndoe2@email.com',
            ],
        ];

        $response = $this->actingAs($user)
            ->postJson('/v1/messages', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Message created successfully!',
            ]);

        $this->assertDatabaseHas('messages', [
            'title' => 'Test Title',
            'content' => 'Test Content',
            'sender_id' => $user->id,
        ]);

        $this->assertDatabaseHas('message_emails', [
            'email' => 'johndoe1@email.com',
        ]);

        $this->assertDatabaseHas('message_emails', [
            'email' => 'johndoe2@email.com',
        ]);
    }

    public function test_it_does_not_insert_logged_user_email()
    {
        $user = User::factory()->create([
            'email' => 'user@email.com',
        ]);

        $payload = [
            'title' => 'Test Title',
            'content' => 'Test Content',
            'should_notify_at' => now()->addDay()->toDateTimeString(),
            'emails' => [
                'user@email.com', 
                'other@email.com',
            ],
        ];

        $this->actingAs($user)
            ->postJson('/v1/messages', $payload)
            ->assertStatus(201);

        $this->assertDatabaseCount('message_emails', 1);

        $this->assertDatabaseHas('message_emails', [
            'email' => 'other@email.com',
        ]);

        $this->assertDatabaseMissing('message_emails', [
            'email' => 'user@email.com',
        ]);
    }

    public function test_it_creates_message_without_emails()
    {
        $user = User::factory()->create();

        $payload = [
            'title' => 'No Emails',
            'content' => 'Only message',
            'should_notify_at' => now()->addDay()->toDateTimeString(),
        ];

        $this->actingAs($user)
            ->postJson('/v1/messages', $payload)
            ->assertStatus(201);

        $this->assertDatabaseHas('messages', [
            'title' => 'No Emails',
        ]);

        $this->assertDatabaseCount('message_emails', 0);
    }

    public function test_it_requires_authentication()
    {
        $payload = [
            'title' => 'Test',
            'content' => 'Test',
            'should_notify_at' => now()->addDay()->toDateTimeString(),
        ];

        $this->postJson('/v1/messages', $payload)
            ->assertStatus(401);
    }
}
