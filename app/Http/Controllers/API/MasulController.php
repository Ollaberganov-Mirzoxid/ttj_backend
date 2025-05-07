<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MasulController extends Controller
{
    // Barcha masullarni ko‘rish
    public function index(Request $request) // $request ni qo‘shdim
    {
        if (!$request->user()->hasAnyRole(['super_admin', 'masul'])) {
            return response()->json(['message' => 'Ruxsat yo‘q'], 403);
        }
        
        $masullar = User::role('masul')->get();
        return response()->json($masullar);
    }

    // Yangi masul yaratish
    public function store(Request $request) // $request ni qo‘shdim
    {
        if (!$request->user()->hasRole('super_admin')) {
            return response()->json(['message' => 'Ruxsat yo‘q'], 403);
        }

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $masul = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $masul->assignRole('masul');

        return response()->json(['message' => 'Masʼul shaxs yaratildi', 'masul' => $masul]);
    }

    // Masulni yangilash
    public function update(Request $request, $id) // $request ni qo‘shdim
    {
        if (!$request->user()->hasRole('super_admin')) {
            return response()->json(['message' => 'Ruxsat yo‘q'], 403);
        }

        $masul = User::findOrFail($id);

        $masul->update($request->only(['name', 'email']));

        return response()->json(['message' => 'Yangilandi', 'masul' => $masul]);
    }

    // Masulni o‘chirish
    public function destroy(Request $request, $id) // $request ni qo‘shdim
    {
        if (!$request->user()->hasRole('super_admin')) {
            return response()->json(['message' => 'Ruxsat yo‘q'], 403);
        }

        $masul = User::find($id);
        $masul->delete();

        return response()->json(['message' => 'O‘chirildi']);
    }
}
