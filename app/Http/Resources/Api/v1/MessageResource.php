<?php

namespace App\Http\Resources\Api\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'should_notify_at' => $this->should_notify_at,
            'sender' => [
                'name' => $this->sender->name,
                'email' => $this->sender->email,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];;
    }
}
