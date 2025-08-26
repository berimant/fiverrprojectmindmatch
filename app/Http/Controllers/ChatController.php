<?php
namespace App\Http\Controllers;

use App\Models\MatchFriends;
use App\Models\Message;
use App\Models\Friend;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    protected $interestController;

    public function __construct(InterestController $interestController)
    {
        $this->interestController = $interestController;
    }

    public function getChat2(Request $request, $match_id)
    {
        $messages = Message::where('match_id', $match_id)->get();
        return response()->json(['messages' => $messages], 200);
    }

     public function getChat(Request $request, $match_id)
    {
        // Solusi yang disarankan: Periksa apakah match masih ada
        $match = MatchFriends::find($match_id);
        if (!$match) {
            // Jika match tidak ditemukan, kembalikan 404 Not Found
            return response()->json(['message' => 'Match not found'], 404);
        }

        $messages = Message::where('match_id', $match_id)->get();
        return response()->json(['messages' => $messages], 200);
    }

    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'match_id' => 'required|exists:matches,id',
            'sender_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $match = MatchFriends::find($request->match_id);
        if (!$match || ($match->user1_id != $request->sender_id && $match->user2_id != $request->sender_id)) {
            return response()->json(['message' => 'Invalid match or sender'], 403);
        }

        $message = Message::create([
            'match_id' => $request->match_id,
            'sender_id' => $request->sender_id,
            'content' => $request->content
        ]);
        return response()->json(['message' => $message], 201);
    }

    public function addFriend(Request $request)
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

        $match = MatchFriends::where(function ($q) use ($userId, $friendId) {
            $q->where('user1_id', $userId)->where('user2_id', $friendId);
            $q->orWhere('user1_id', $friendId)->where('user2_id', $userId);
        })->first();

        if (!$match) {
            return response()->json(['message' => 'Users are not in a match'], 403);
        }

        if (Friend::where('user_id', $userId)->where('friend_id', $friendId)->exists()) {
            return response()->json(['message' => 'Already friends'], 400);
        }

        Friend::create(['user_id' => $userId, 'friend_id' => $friendId]);
        Friend::create(['user_id' => $friendId, 'friend_id' => $userId]);

        return response()->json([
            'message' => 'Friend added',
            'friend' => [
                'id' => $friendId,
                'name' => User::find($friendId)->name,
                'phone_number' => User::find($friendId)->phone_number,
                'profile_status' => User::find($friendId)->profile_status
            ]
        ], 201);
    }

    public function deleteChat(Request $request, $match_id)
    {
        $validator = Validator::make(array_merge($request->all(), ['match_id' => $match_id]), [
            'match_id' => 'required|exists:matches,id',
            'user_id' => 'required|exists:users,id',
            'clear_only' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $userId = $request->user_id;
        $match = MatchFriends::find($match_id);

        if (!$match || ($match->user1_id != $userId && $match->user2_id != $userId)) {
            return response()->json(['message' => 'Invalid match or user'], 403);
        }

        Message::where('match_id', $match_id)->delete();

        if (!$request->input('clear_only', false)) {
            $match->delete();
        }

        return response()->json([], 200); // Return empty body for Response<Unit>
    }

    // di dalam file App/Http/Controllers/ChatController.php

// ... (kode yang sudah ada di atas)

public function checkActiveMatch(Request $request, $user_id)
{
    $match = MatchFriends::where('is_active', true)
                         ->where(function ($query) use ($user_id) {
                             $query->where('user1_id', $user_id)
                                   ->orWhere('user2_id', $user_id);
                         })
                         ->first();

    if ($match) {
        return response()->json([
            'message' => 'Active match found',
            'match_id' => $match->id
        ], 200);
    } else {
        return response()->json([
            'message' => 'No active match found'
        ], 404);
    }
}
}