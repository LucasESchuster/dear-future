<?php

namespace Tests\Feature\v1;

use App\Models\Api\v1\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowMessageTest extends TestCase
{
     use RefreshDatabase;

    /** @test */
    public function it_returns_the_message_when_it_belongs_to_authenticated_user()
    {
        $user = User::factory()->create();

        $message = Message::factory()->create([
            'sender_id' => $user->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson("/v1/messages/{$message->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $message->id,
                'title' => $message->title,
                'content' => $message->content,
                'should_notify_at' => optional($message->should_notify_at)?->toJSON(),
                'sender' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ])
            ->assertJsonStructure([
                'id',
                'title',
                'content',
                'should_notify_at',
                'sender' => [
                    'name',
                    'email',
                ],
                'created_at',
                'updated_at',
            ]);
    }

    /** @test */
    public function it_returns_404_when_message_does_not_exist()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/v1/messages/999");

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Message not found',
            ]);
    }

    /** @test */
    public function it_returns_404_when_message_belongs_to_another_user()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();

        $message = Message::factory()->create([
            'sender_id' => $anotherUser->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson("/v1/messages/{$message->id}");

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Message not found',
            ]);
    }
}
