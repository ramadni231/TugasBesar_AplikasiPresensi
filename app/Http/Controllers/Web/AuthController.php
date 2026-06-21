<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }


    public function login(Request $request)
    {
        $request->validate([
            'nomor_identitas' => 'required',
            'password' => 'required',
        ]);

        $pengguna = Pengguna::where('nomor_identitas', $request->nomor_identitas)->first();

        if (!$pengguna || !Hash::check($request->password, $pengguna->password)) {
            return back()->withErrors([
                'nomor_identitas' => 'Nomor identitas atau password salah.',
            ])->withInput();
        }

        Auth::login($pengguna);
        $request->session()->regenerate();

        if ($pengguna->peran === 'admin') {
            return redirect()->intended('/admin/dashboard');
        } elseif ($pengguna->peran === 'dosen') {
            return redirect()->intended('/dosen/dashboard');
        } else {
            return redirect()->intended('/mahasiswa/dashboard');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showProfil()
    {
        return view('auth.profil');
    }

    public function updateProfil(Request $request)
    {
        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password_lama, $user->password)) {
            return back()->withErrors(['password_lama' => 'Password lama tidak sesuai.']);
        }

        Pengguna::where('id', $user->id)->update([
            'password' => Hash::make($request->password_baru)
        ]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
