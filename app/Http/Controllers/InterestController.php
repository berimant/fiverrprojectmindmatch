<?php
namespace App\Http\Controllers;

use App\Models\Interest;
use App\Models\User;
use App\Models\Message;
use App\Models\MatchFriends; // Gunakan MatchFriends
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InterestController extends Controller
{
    // ini interest user per id
    public function getInterests(Request $request)
    {
        try {
            $userId = $request->query('user_id');
            if (!$userId) {
                return response()->json(['message' => 'User ID required'], 400);
            }
            $user = User::find($userId);
            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }
            $interests = $user->interests()->get()->map(function ($interest) {
                return [
                    'id' => $interest->id,
                    'name' => $interest->name,
                    'created_at' => $interest->created_at
                ];
            });
            return response()->json(['message' => 'Interests retrieved', 'data' => $interests], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to get interests', 'error' => $e->getMessage()], 500);
        }
    }

    // New method to get ALL available interests from the interests table
    public function getAllInterests(Request $request)
    {
        try {
            $interests = Interest::all()->map(function ($interest) {
                return [
                    'id' => $interest->id,
                    'name' => $interest->name,
                    'created_at' => $interest->created_at
                ];
            });
            return response()->json(['message' => 'All interests retrieved', 'data' => $interests], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to get all interests', 'error' => $e->getMessage()], 500);
        }
    }

    public function addInterest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255|unique:interests,name',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $interest = Interest::firstOrCreate(['name' => $request->name]);
        $user = User::find($request->user_id);
        $user->interests()->syncWithoutDetaching([$interest->id]);

        return response()->json(['message' => 'Interest added', 'interest' => [
            'id' => $interest->id,
            'name' => $interest->name,
            'created_at' => $interest->created_at
        ]], 201);
    }

    public function addUserInterest(Request $request)
{
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|exists:users,id',
        'interest_id' => 'required|exists:interests,id',
    ]);

    if ($validator->fails()) {
        return response()->json(['message' => $validator->errors()->first()], 400);
    }

    $user = User::find($request->user_id);
    $interest = Interest::find($request->interest_id);

    // Cek apakah hubungan sudah ada
    if ($user->interests()->where('interest_id', $request->interest_id)->exists()) {
        return response()->json(['message' => 'User already has this interest'], 400);
    }

    // Tambahkan hubungan ke user_interests
    $user->interests()->attach($request->interest_id);

    return response()->json([
        'message' => 'User interest added',
        'user_id' => $user->id,
        'interest_id' => $interest->id,
        'interest_name' => $interest->name
    ], 201);
}

    public function deleteInterest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'interest_id' => 'required|exists:interests,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $user = User::find($request->user_id);
        $user->interests()->detach($request->interest_id);

        return response()->json(['message' => 'Interest removed'], 200);
    }

    public function deleteInterestFromTable(Request $request, $id)
{
    $interest = Interest::find($id);
    if (!$interest) {
        return response()->json(['message' => 'Interest not found'], 404);
    }

    // Hapus minat (hubungan di user_interests akan otomatis terhapus karena cascade)
    $interest->delete();

    return response()->json(['message' => 'Interest deleted from interests table'], 200);
}

public function match(Request $request)
{
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|exists:users,id',
        'interest_ids' => 'required|array',
        'interest_ids.*' => 'exists:interests,id',
        'mode' => 'required|in:online,offline',
    ]);

    if ($validator->fails()) {
        return response()->json(['message' => $validator->errors()->first()], 400);
    }

    $userId = $request->user_id;
    $interestIds = $request->interest_ids;
    $mode = $request->mode;

    $user = User::find($userId);
    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $user->is_online = ($mode === 'online') ? 1 : 0;
    $user->save();

    // Delete all existing matches and messages for this user
    $existingMatches = MatchFriends::where('user1_id', $userId)->orWhere('user2_id', $userId)->get();
    foreach ($existingMatches as $existingMatch) {
        Message::where('match_id', $existingMatch->id)->delete();
        $existingMatch->delete();
    }

    // Find a new match
    $query = User::whereHas('interests', function ($q) use ($interestIds) {
        $q->whereIn('interests.id', $interestIds);
    })
        ->where('id', '!=', $userId);

    if ($mode === 'online') {
        $query->where('is_online', 1);
    }

    $matchedUser = $query->inRandomOrder()->first();

    if (!$matchedUser) {
        return response()->json(['message' => 'No match found'], 404);
    }

    // Create new match
    $match = MatchFriends::create([
        'user1_id' => $userId,
        'user2_id' => $matchedUser->id,
        'is_active' => true,
        'mode' => $mode,
    ]);

    return response()->json([
        'message' => 'Match found',
        'match_id' => $match->id,
        'matches' => [
            [
                'id' => $matchedUser->id,
                'phone_number' => $matchedUser->phone_number,
                'name' => $matchedUser->name,
                'profile_status' => $matchedUser->profile_status
            ]
        ]
    ], 200);
}

 public function match2(Request $request)
{
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|exists:users,id',
        'interest_ids' => 'required|array',
        'interest_ids.*' => 'exists:interests,id',
        'mode' => 'required|in:online,offline',
    ]);

    if ($validator->fails()) {
        return response()->json(['message' => $validator->errors()->first()], 400);
    }

    $userId = $request->user_id;
    $interestIds = $request->interest_ids;
    $mode = $request->mode;

    $user = User::find($userId);
    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $user->is_online = ($mode === 'online') ? 1 : 0;
    $user->save();

    $query = User::whereHas('interests', function ($q) use ($interestIds) {
        $q->whereIn('interests.id', $interestIds);
    })
        ->where('id', '!=', $userId)
        ->whereDoesntHave('matchFriends', function ($q) use ($userId) {
            $q->where('is_active', true)
              ->where(function ($q) use ($userId) {
                  $q->where('user1_id', $userId)->orWhere('user2_id', $userId);
              });
        });

    if ($mode === 'online') {
        $query->where('is_online', 1);
    }

    $matchedUser = $query->inRandomOrder()->first();

    if (!$matchedUser) {
        return response()->json(['message' => 'No match found'], 404);
    }

    $existingMatch = MatchFriends::where(function ($q) use ($userId, $matchedUser) {
        $q->where('user1_id', $userId)->where('user2_id', $matchedUser->id);
    })->orWhere(function ($q) use ($userId, $matchedUser) {
        $q->where('user1_id', $matchedUser->id)->where('user2_id', $userId);
    })->where('is_active', true)->first();

    if ($existingMatch) {
        return response()->json([
            'message' => 'Match already exists',
            'match_id' => $existingMatch->id,
            'matches' => [
                [
                    'id' => $matchedUser->id,
                    'phone_number' => $matchedUser->phone_number,
                    'name' => $matchedUser->name,
                    'profile_status' => $matchedUser->profile_status
                ]
            ]
        ], 200);
    }

    $match = MatchFriends::create([
        'user1_id' => $userId,
        'user2_id' => $matchedUser->id,
        'is_active' => true,
        'mode' => $mode,
    ]);

    return response()->json([
        'message' => 'Match found',
        'match_id' => $match->id,
        'matches' => [
            [
                'id' => $matchedUser->id,
                'phone_number' => $matchedUser->phone_number,
                'name' => $matchedUser->name,
                'profile_status' => $matchedUser->profile_status
            ]
        ]
    ], 200);
}


// Fungsi baru untuk memperbarui status online
    public function updateUserOnlineStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'is_online' => 'required|integer|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->is_online = $request->is_online;
        $user->save();

        return response()->json([
            'message' => 'User online status updated successfully.',
            'is_online' => $user->is_online
        ], 200);
    }

}