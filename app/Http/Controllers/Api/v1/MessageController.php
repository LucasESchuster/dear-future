<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\StoreMessageRequest;
use App\Http\Resources\Api\v1\MessageResource;
use App\Models\Api\v1\Message;
use App\Models\Api\v1\MessageEmail;

class MessageController extends Controller
{

    public function index()
    {
        $messages = Message::where('sender_id', request()->user()->id)->get();

        return response()->json(MessageResource::collection($messages));
    }

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

    public function show($id)
    {
        $message = Message::find($id);

        if (!$message || $message->sender_id !== request()->user()->id) {
            return response()->json(['message' => 'Message not found'], 404);
        }

        return response()->json(new MessageResource($message));
    }

    public function destroy($id)
    {
        $user = request()->user();

        $message = Message::find($id);

        if (!$message) {
            return response()->json(['message' => 'Message not found'], 404);
        }

        if ($user->id !== $message->sender_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $message->delete();

        return response()->json([
            'message' => 'Message deleted successfully!',
        ]);
    }
}
