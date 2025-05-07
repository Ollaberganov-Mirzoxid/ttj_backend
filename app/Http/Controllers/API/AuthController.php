<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //Roli Talaba bo'lgan Userlarni olish
    public function index(Request $request)
    {
        // Faqat talaba roli bor userlar
        $users = User::role('talaba')->get();

        return response()->json($users);
    }

    // Ro‘yxatdan o‘tish
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('talaba');

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    // Login
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
                'role' => $user->getRoleNames()[0] ?? null // ['superadmin'] bo‘lishi mumkin, shuning uchun [0]
            ]);
        }   
        return response()->json(['message' => 'Login xato!'], 401);
    }


    // Logout
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Tizimdan chiqdingiz']);
    }

    // Foydalanuvchi haqida ma’lumot
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
