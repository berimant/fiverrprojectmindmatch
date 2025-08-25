<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:8',
            'name' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $user = User::create([
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'name' => $request->name,
            'profile_status' => 'anonim',
        ]);

        return response()->json([
            'message' => 'User registered',
            'user' => [
                'id' => $user->id,
                'phone_number' => $user->phone_number,
                'name' => $user->name,
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|max:20',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $user = User::where('phone_number', $request->phone_number)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Nomor telepon atau kata sandi salah'], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'phone_number' => $user->phone_number,
                'name' => $user->name,
            ]
        ]);
    }
}