<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Tampilkan semua user
    public function index()
    {
        $users = User::paginate(10);
        return view('admin.users.index', compact('users'));
    }

    // Simpan user baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip_nik'   => 'required|string|max:30|unique:users,nip_nik',
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|max:100|unique:users,email',
            'position'  => 'nullable|string|max:50',
            'division'  => 'nullable|string|max:50',
            'address'   => 'nullable|string|max:200',
            'role'      => 'required|in:admin,kepala,pegawai',
            'photo_url' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
            'password'  => 'required|string|min:6|confirmed',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);
        // Optional: log activity jika masih dipakai
        ActivityLogger::log('create', 'user', 'Tambah user: ' . $request->name);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambah.');
    }

    // Update user
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nip_nik'   => 'required|string|max:30|unique:users,nip_nik,' . $user->id,
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|max:100|unique:users,email,' . $user->id,
            'position'  => 'nullable|string|max:50',
            'division'  => 'nullable|string|max:50',
            'address'   => 'nullable|string|max:200',
            'role'      => 'required|in:admin,kepala,pegawai',
            'photo_url' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
            'password'  => 'nullable|string|min:6|confirmed',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        // Optional: log activity jika masih dipakai
        ActivityLogger::log('update', 'user', 'Update user: ' . $user->name);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diupdate.');
    }

    // Hapus user
    public function destroy(User $user)
    {
        $userName = $user->name;
        $user->delete();
        // Optional: log activity jika masih dipakai
        ActivityLogger::log('delete', 'user', 'Hapus user: ' . $userName);
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}
