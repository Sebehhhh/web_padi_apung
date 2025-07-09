<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan form login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Proses login pengguna: bisa email atau username,
     * plus cek is_active = 1.
     */
    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string', // input: email atau username
            'password' => 'required|string',
        ]);

        // Tentukan tipe login: email atau username
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Kredensial termasuk is_active
        $credentials = [
            $loginType    => $request->login,
            'password'    => $request->password,
            'is_active'   => 1,
        ];

        // Coba login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect berdasar role
            $user = Auth::user();
            switch ($user->role) {
                case 'admin':
                    return redirect()
                        ->route('admin.dashboard')
                        ->with('success', 'Selamat datang di Dashboard Admin');
                case 'kepala':
                    return redirect()
                        ->route('kepala.dashboard')
                        ->with('success', 'Selamat datang di Dashboard Kepala');
                case 'pegawai':
                    return redirect()
                        ->route('pegawai.dashboard')
                        ->with('success', 'Selamat datang di Dashboard Pegawai');
                default:
                    Auth::logout();
                    return redirect()
                        ->route('login')
                        ->withErrors(['login' => 'Role tidak dikenal.']);
            }
        }

        // Jika gagal, periksa apakah akun ada tapi non-aktif
        $user = User::where($loginType, $request->login)->first();
        if ($user
            && Hash::check($request->password, $user->password)
            && ! $user->is_active
        ) {
            return back()
                ->withErrors(['login' => 'Akun Anda belum aktif.'])
                ->onlyInput('login');
        }

        // Default: salah email/username atau password
        return back()
            ->withErrors(['login' => 'Username/email atau password salah.'])
            ->onlyInput('login');
    }

    /**
     * Logout pengguna.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('status', 'Anda berhasil logout');
    }
}