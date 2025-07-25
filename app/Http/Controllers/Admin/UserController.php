<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Tampilkan semua user
    public function index(Request $request)
    {
        $query = User::query();

        // Filter berdasarkan role jika ada
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter berdasarkan status aktif jika ada
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Filter pencarian nama/email/nip_nik jika ada
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nip_nik', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(10)->appends($request->except('page'));

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

        $user = User::create($validated);

        // Logging activity: siapa yang create siapa
        ActivityLogger::log(
            'create',
            'user',
            'Tambah user: ' . $user->name . ' (ID: ' . $user->id . ') oleh: ' . Auth::user()->name
        );

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

        ActivityLogger::log(
            'update',
            'user',
            'Update user: ' . $user->name . ' (ID: ' . $user->id . ') oleh: ' . Auth::user()->name
        );

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diupdate.');
    }

    // Hapus user
    public function destroy(User $user)
    {
        $userName = $user->name;
        $userId = $user->id;
        $user->delete();

        ActivityLogger::log(
            'delete',
            'user',
            'Hapus user: ' . $userName . ' (ID: ' . $userId . ') oleh: ' . Auth::user()->name
        );

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}