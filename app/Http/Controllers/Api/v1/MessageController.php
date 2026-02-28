<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\StoreMessageRequest;
use App\Models\Api\v1\Message;
use App\Models\Api\v1\MessageEmail;

class MessageController extends Controller
{
    public function store(StoreMessageRequest $request)
    {
        $validated = $request->validated();

        $message = Message::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'should_notify_at' => $validated['should_notify_at'],
            'sender_id' => $request->user()->id,
        ]);

        $emails = collect($validated['emails'] ?? [])->map(function ($email) use ($message, $request) {
            if ($email == $request->user()->email) {
                return null;
            }
            return [
                'email' => $email,
                'message_id' => $message->id,
            ];
        })->filter();

        MessageEmail::insert($emails->toArray());

        return response()->json([
            'message' => 'Message created successfully!',
        ], 201);
    }
}
