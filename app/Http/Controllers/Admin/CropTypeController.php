<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CropType;
use Illuminate\Http\Request;

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
        CropType::create($validated);

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

        return redirect()->route('admin.crop-types.index')->with('success', 'Jenis tanaman berhasil diupdate.');
    }

    // Hapus jenis tanaman
    public function destroy(CropType $crop_type)
    {
        $crop_type->delete();
        return redirect()->route('admin.crop-types.index')->with('success', 'Jenis tanaman berhasil dihapus.');
    }
}