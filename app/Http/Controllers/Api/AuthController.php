<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle Login
     */
    public function login(Request $request)
    {
        $request->validate([
            'nomor_identitas' => 'required',
            'password' => 'required',
        ]);

        $pengguna = Pengguna::where('nomor_identitas', $request->nomor_identitas)->first();

        if (!$pengguna || !Hash::check($request->password, $pengguna->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nomor identitas atau password salah.',
            ], 401);
        }

        $token = $pengguna->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'data' => [
                'token' => $token,
                'pengguna' => $pengguna
            ]
        ]);
    }

    /**
     * Handle Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil keluar',
        ]);
    }

    /**
     * Get Profile
     */
    public function profile(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => $request->user(),
        ]);
    }

    /**
     * Update Password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:6|confirmed',
        ]);

        $pengguna = $request->user();

        if (!Hash::check($request->password_lama, $pengguna->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Password lama tidak sesuai.',
            ], 422);
        }

        $pengguna->update([
            'password' => Hash::make($request->password_baru)
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Password berhasil diperbarui',
        ]);
    }
}
