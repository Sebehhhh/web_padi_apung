<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CropType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityLogger;

class CropTypeController extends Controller
{
    // Tampilkan semua crop type (pagination)
    public function index()
    {
        $crops = CropType::orderBy('name')->paginate(10);
        return view('admin.crop_types.index', compact('crops'));
    }

    // Simpan jenis tanaman baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:50|unique:crop_types,name',
            'description' => 'nullable|string|max:100',
            'is_active'   => 'required|boolean',
        ]);
        $crop = CropType::create($validated);

        // Logging
        ActivityLogger::log(
            'create',
            'crop_type',
            'Tambah jenis tanaman: ' . $crop->name . ' oleh user: ' . Auth::user()->name
        );

        return redirect()->route('admin.crop-types.index')->with('success', 'Jenis tanaman berhasil ditambah.');
    }

    // Update jenis tanaman
    public function update(Request $request, CropType $crop_type)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:50|unique:crop_types,name,' . $crop_type->id,
            'description' => 'nullable|string|max:100',
            'is_active'   => 'required|boolean',
        ]);
        $crop_type->update($validated);

        // Logging
        ActivityLogger::log(
            'update',
            'crop_type',
            'Update jenis tanaman: ' . $crop_type->name . ' oleh user: ' . Auth::user()->name
        );

        return redirect()->route('admin.crop-types.index')->with('success', 'Jenis tanaman berhasil diupdate.');
    }

    // Hapus jenis tanaman
    public function destroy(CropType $crop_type)
    {
        $name = $crop_type->name;
        $crop_type->delete();

        // Logging
        ActivityLogger::log(
            'delete',
            'crop_type',
            'Hapus jenis tanaman: ' . $name . ' oleh user: ' . Auth::user()->name
        );

        return redirect()->route('admin.crop-types.index')->with('success', 'Jenis tanaman berhasil dihapus.');
    }
}
