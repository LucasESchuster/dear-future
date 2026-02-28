<?php

namespace Tests\Feature\v1;

use App\Models\Api\v1\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;

class MessageDestroyTest extends TestCase
{
    use RefreshDatabase;
    public function test_guest_cannot_delete_message()
    {
        $user = User::factory()->create();
        $message = Message::factory()->create([
            'sender_id' => $user->id
        ]);

        $response = $this->deleteJson("/v1/messages/{$message->id}");

        $response->assertStatus(401); 
    }

    public function test_returns_404_when_message_not_found()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->deleteJson("/v1/messages/9999");

        $response->assertStatus(404)
                 ->assertJson([
                     'message' => 'Message not found'
                 ]);
    }
    public function test_user_cannot_delete_message_from_another_user()
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();

        $message = Message::factory()->create([
            'sender_id' => $owner->id
        ]);

        $this->actingAs($attacker);

        $response = $this->deleteJson("/v1/messages/{$message->id}");

        $response->assertStatus(403)
                 ->assertJson([
                     'message' => 'Unauthorized'
                 ]);

        $this->assertDatabaseHas('messages', [
            'id' => $message->id
        ]);
    }

    public function test_user_can_delete_own_message()
    {
        $user = User::factory()->create();

        $message = Message::factory()->create([
            'sender_id' => $user->id
        ]);

        $this->actingAs($user);

        $response = $this->deleteJson("/v1/messages/{$message->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Message deleted successfully!'
                 ]);

        $this->assertDatabaseMissing('messages', [
            'id' => $message->id
        ]);
    }
}
