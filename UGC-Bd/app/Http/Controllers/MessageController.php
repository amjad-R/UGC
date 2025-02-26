<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use App\Models\Service;
use App\Models\Chat;
use Illuminate\Http\Request;

class MessageController extends Controller
{

    public function showMessages($serviceId)
    {
        $service = Service::findOrFail($serviceId);

        $chat = Chat::where('sender_id', auth()->id())
            ->where('receiver_id', $service->user_id)
            ->first();

        if (!$chat) {
            return response()->json([
                'success' => true,
                'messages' => [],
            ]);
        }

        $messages = Message::where('chat_id', $chat->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }



    public function sendMessage2(Request $request, $chat_id)
    {
        $data = $request->validate([
            'content' => 'required|string',
        ]);

        $chat = Chat::findOrFail($chat_id);

        if ($chat->sender_id != auth()->id() && $chat->receiver_id != auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message = Message::create([
            'chat_id' => $chat_id,
            'sender_id' => auth()->id(),
            'content' => $data['content'],
        ]);

        return response()->json([
            'success' => true,
            'message' => $message,
        ], 201);
    }


    public function sendMessage(Request $request, $serviceId)
    {
        $validatedData = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        if (!$serviceId) {
            return response()->json(['error' => 'Service ID is required.'], 400);
        }

        $service = Service::findOrFail($serviceId);

        $chat = Chat::where(function ($query) use ($service) {
            $query->where('sender_id', auth()->id())
                ->where('receiver_id', $service->user_id);
        })
            ->orWhere(function ($query) use ($service) {
                $query->where('sender_id', $service->user_id)
                    ->where('receiver_id', auth()->id());
            })
            ->first();

        if (!$chat) {
            $chat = Chat::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $service->user_id,
            ]);
        }

        $message = Message::create([
            'chat_id' => $chat->id,
            'sender_id' => auth()->id(),
            'content' => $validatedData['message'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => $message,
        ]);
    }

    public function store(Request $request, $serviceId)
    {
        $request->validate([
            'message' => 'required|string|max:255',
        ]);

        $service = Service::findOrFail($serviceId);

        $message = Message::create([
            'content' => $request->message,
            'service_id' => $service->id,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => $message,
        ]);
    }




    public function sendMessageToChat(Request $request, $chat_id)
    {
        $data = $request->validate([
            'content' => 'required|string',
        ]);

        $chat = Chat::findOrFail($chat_id);

        if ($chat->sender_id != auth()->id() && $chat->receiver_id != auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message = Message::create([
            'chat_id' => $chat_id,
            'sender_id' => auth()->id(),
            'content' => $data['content'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => $message,
        ]);
    }



    public function sendSupportMessage(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $supportId = 999;

        $chat = Chat::where('sender_id', auth()->id())
            ->where('receiver_id', $supportId)
            ->where('is_support', true)
            ->first();

        if (!$chat) {
            $chat = Chat::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $supportId,
                'is_support' => true,
            ]);
        }

        $message = Message::create([
            'chat_id' => $chat->id,
            'sender_id' => auth()->id(),
            'content' => $validated['content'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully to support',
            'data' => $message,
        ], 201);
    }




    public function getSupportChat()
    {
        $supportId = 999;

        $chat = Chat::where('sender_id', auth()->id())
            ->where('receiver_id', $supportId)
            ->where('is_support', true)
            ->first();

        if (!$chat) {
            return response()->json(['error' => 'No chat found with support'], 404);
        }

        $messages = Message::where('chat_id', $chat->id)->get();

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }
}
