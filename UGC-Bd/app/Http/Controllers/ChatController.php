<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $chats = Chat::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->with([
                'messages' => function ($query) {
                    $query->latest('created_at');
                },
                'sender:id,first_name,last_name',
                'receiver:id,first_name,last_name',
            ])
            ->get();

        $chatData = $chats->map(function ($chat) use ($user) {
            $lastMessage = $chat->messages->first();
            $otherUser = $chat->sender_id === $user->id ? $chat->receiver : $chat->sender;

            return [
                'id' => $chat->id,
                'name' => ($otherUser?->first_name ?? 'Unknown') . ' ' . ($otherUser?->last_name ?? ''),
                'avatar' => $otherUser?->profile->profile_image ?? 'default.png',
                'lastMessage' => $lastMessage?->content ?? 'No messages yet',
                'lastActive' => $lastMessage?->created_at?->diffForHumans() ?? 'N/A',
            ];
        });

        return response()->json($chatData);
    }




    public function show($chatId)
    {
        $chat = Chat::findOrFail($chatId);

        $user = Auth::user();
        if ($chat->sender_id !== $user->id && $chat->receiver_id !== $user->id) {
            return response()->json(['error' => 'You do not have access to this chat'], 403);
        }

        $messages = $chat->messages()
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'chat' => $chat,
            'messages' => $messages,
            'current_user_id' => $user->id
        ]);
    }

    public function showseport($chatId)
    {
        $user = Auth::user();

        if ($user->user_type === 'support') {
            $chat = Chat::findOrFail($chatId);

            $messages = $chat->messages()
                ->with('sender.profile')
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'chat' => $chat,
                'messages' => $messages,
                'current_user_id' => $user->id,
            ]);
        }

        $chat = Chat::where('id', $chatId)
            ->where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            })
            ->firstOrFail();

        $messages = $chat->messages()
            ->with('sender.profile')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'chat' => $chat,
            'messages' => $messages,
            'current_user_id' => $user->id,
        ]);
    }







    public function sendMessage(Request $request)
    {
        $request->validate([
            'chat_id' => 'required|integer|exists:chats,id',
            'content' => 'required|string',
        ]);

        $chat = Chat::where('id', $request->chat_id)
            ->where(function ($query) {
                $query->where('sender_id', auth()->id())
                    ->orWhere('receiver_id', auth()->id());
            })
            ->firstOrFail();

        $message = Message::create([
            'chat_id' => $chat->id,
            'sender_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return response()->json(['message' => $message], 201);
    }


    public function getUserChats(Request $request)
    {
        $userId = $request->user()->id;

        $chats = Chat::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->with(['sender:id,first_name', 'receiver:id,first_name'])
            ->get();

        $formattedChats = $chats->map(function ($chat) use ($userId) {
            return [
                'chat_id' => $chat->id,
                'last_message' => $chat->messages()->latest()->value('content'),
                'sender_name' => $chat->sender_id === $userId ? 'You' : $chat->sender->first_name,
                'receiver_name' => $chat->receiver_id === $userId ? 'You' : $chat->receiver->first_name,
            ];
        });

        return response()->json($formattedChats);
    }


    public function getChatMessages($chatId, Request $request)
    {
        $userId = $request->user()->id;

        $chat = Chat::where('id', $chatId)
            ->where(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                    ->orWhere('receiver_id', $userId);
            })
            ->firstOrFail();

        $messages = $chat->messages()
            ->with('sender:id,first_name,last_name,profile_image')
            ->orderBy('created_at', 'asc')
            ->get();


        return response()->json(['messages' => $messages]);
    }

    public function chatList(Request $request)
    {
        $user = $request->user();

        $chats = Chat::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhere('support_id', $user->id);
        })
            ->with(['lastMessage', 'user:id,name,avatar'])
            ->get()
            ->map(function ($chat) use ($user) {
                $otherUser = $chat->user_id == $user->id ? $chat->support : $chat->user;

                return [
                    'id' => $chat->id,
                    'userName' => $otherUser->name ?? 'Unknown User',
                    'userAvatar' => $otherUser->avatar ?? 'default-avatar.png',
                    'lastMessage' => $chat->lastMessage->content ?? 'No messages yet',
                    'lastMessageTime' => $chat->lastMessage->created_at ?? null,
                ];
            });

        return response()->json($chats);
    }



    public function indexSupportChats()
    {
        $chats = Chat::where('is_support', 1)
            ->with([
                'messages' => function ($query) {
                    $query->latest('created_at');
                },
                'sender:id,first_name,last_name',
                'receiver:id,first_name,last_name',
            ])
            ->get();

        $chatData = $chats->map(function ($chat) {
            $lastMessage = $chat->messages->first();
            $otherUser = $chat->receiver;

            return [
                'id' => $chat->id,
                'name' => ($otherUser?->first_name ?? 'Support') . ' ' . ($otherUser?->last_name ?? ''),
                'avatar' => $otherUser?->profile->profile_image ?? 'default.png',
                'lastMessage' => $lastMessage?->content ?? 'No messages yet',
                'lastActive' => $lastMessage?->created_at?->diffForHumans() ?? 'N/A',
            ];
        });

        return response()->json([
            'success' => true,
            'chats' => $chatData,
        ]);
    }
    public function getSupportMessages()
    {
        $messages = Message::whereHas('chat', function ($query) {
            $query->where('is_support', 1);
        })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    public function getSupportChats()
    {
        $chats = Chat::where('is_support', 1)
            ->with([
                'messages' => function ($query) {
                    $query->latest('created_at');
                },
                'sender:id,first_name,last_name,profile_image',
                'receiver:id,first_name,last_name,profile_image',
            ])
            ->get();

        $chatData = $chats->map(function ($chat) {
            $lastMessage = $chat->messages->first();

            $participant = $chat->sender ?? $chat->receiver;
            return [
                'id' => $chat->id,
                'name' => "{$participant->first_name} {$participant->last_name}",
                'avatar' => $participant?->profile->profile_image ?? 'default-avatar.png',
                'lastMessage' => $lastMessage?->content ?? 'No messages yet',
                'lastActive' => $lastMessage?->created_at?->diffForHumans() ?? 'N/A',
            ];
        });

        return response()->json($chatData);
    }
    public function sendSupportReply(Request $request, $chatId)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $supportUser = User::where('user_type', 'support')->first();

        if (!$supportUser) {
            return response()->json(['error' => 'Support user not found'], 404);
        }

        $message = Message::create([
            'chat_id' => $chatId,
            'sender_id' => $supportUser->id,
            'content' => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reply sent successfully',
            'data' => $message,
        ]);
    }
}
