<?php
namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\PrivateMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FriendController extends Controller
{
    public function getFriends(Request $request, $user_id)
    {
        $validator = Validator::make(['user_id' => $user_id], [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $friends = Friend::where('user_id', $user_id)
            ->with('friend')
            ->get()
            ->map(function ($friend) {
                return [
                    'id' => $friend->friend->id,
                    'name' => $friend->friend->name,
                    'phone_number' => $friend->friend->phone_number,
                    'profile_status' => $friend->friend->profile_status
                ];
            });

        return response()->json(['message' => 'Friends retrieved', 'friends' => $friends], 200);
    }

    public function addFriend(Request $request)
    {
        // This is handled by ChatController@addFriend, so no changes needed here
        return response()->json(['message' => 'Use /friends endpoint in ChatController'], 400);
    }

    public function getPrivateChat(Request $request, $user_id, $friend_id)
    {
        $validator = Validator::make([
            'user_id' => $user_id,
            'friend_id' => $friend_id
        ], [
            'user_id' => 'required|exists:users,id',
            'friend_id' => 'required|exists:users,id|different:user_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        // Check if they are friends
        if (!Friend::where('user_id', $user_id)->where('friend_id', $friend_id)->exists()) {
            return response()->json(['message' => 'Users are not friends'], 403);
        }

        $messages = PrivateMessage::where(function ($q) use ($user_id, $friend_id) {
            $q->where('sender_id', $user_id)->where('receiver_id', $friend_id);
            $q->orWhere('sender_id', $friend_id)->where('receiver_id', $user_id);
        })
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'sender_id' => $message->sender_id,
                    'receiver_id' => $message->receiver_id,
                    'content' => $message->content,
                    'created_at' => $message->created_at
                ];
            });

        return response()->json(['message' => 'Private chat retrieved', 'messages' => $messages], 200);
    }

    public function sendPrivateMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sender_id' => 'required|exists:users,id',
            'receiver_id' => 'required|exists:users,id|different:sender_id',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $senderId = $request->sender_id;
        $receiverId = $request->receiver_id;

        // Check if they are friends
        if (!Friend::where('user_id', $senderId)->where('friend_id', $receiverId)->exists()) {
            return response()->json(['message' => 'Users are not friends'], 403);
        }

        $message = PrivateMessage::create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'content' => $request->content
        ]);

        return response()->json([
            'message' => 'Private message sent',
            'private_message' => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'receiver_id' => $message->receiver_id,
                'content' => $message->content,
                'created_at' => $message->created_at
            ]
        ], 201);
    }

    public function clearPrivateChat(Request $request)
{
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|exists:users,id',
        'friend_id' => 'required|exists:users,id|different:user_id',
    ]);

    if ($validator->fails()) {
        return response()->json(['message' => $validator->errors()->first()], 400);
    }

    $userId = $request->user_id;
    $friendId = $request->friend_id;

    // Check if they are friends
    if (!Friend::where('user_id', $userId)->where('friend_id', $friendId)->exists()) {
        return response()->json(['message' => 'Users are not friends'], 403);
    }

    // Delete private messages
    PrivateMessage::where(function ($q) use ($userId, $friendId) {
        $q->where('sender_id', $userId)->where('receiver_id', $friendId);
        $q->orWhere('sender_id', $friendId)->where('receiver_id', $userId);
    })->delete();

    return response()->json(['message' => 'Private chat cleared'], 200);
}
}