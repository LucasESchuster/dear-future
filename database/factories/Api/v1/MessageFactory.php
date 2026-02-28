<?php

namespace Database\Factories\Api\v1;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'should_notify_at' => $this->faker->dateTimeBetween('now', '+1 month'),
            'sender_id' => \App\Models\User::factory(),
        ];
    }
}
