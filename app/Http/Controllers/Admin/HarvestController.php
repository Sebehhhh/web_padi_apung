<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Harvest;
use App\Models\CropType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HarvestController extends Controller
{
    // Index: list data panen
    public function index()
    {
        $harvests = Harvest::with(['user', 'cropType'])->orderByDesc('harvest_date')->paginate(10);
        $cropTypes = CropType::where('is_active', 1)->orderBy('name')->get();

        return view('admin.harvests.index', compact('harvests', 'cropTypes'));
    }

    // Store: tambah data panen
    public function store(Request $request)
    {
        $validated = $request->validate([
            'harvest_date'    => 'required|date',
            'crop_type_id'    => 'required|exists:crop_types,id',
            'land_area_m2'    => 'required|numeric|min:0.01',
            'total_weight_kg' => 'required|numeric|min:0.01',
            'quality'         => 'nullable|in:A,B,C',
            'notes'           => 'nullable|string|max:255',
        ]);
        $validated['user_id'] = Auth::id();

        $harvest = Harvest::create($validated);

        ActivityLogger::log('create', 'harvest', 'Tambah data panen: ID ' . $harvest->id);

        return redirect()->route('admin.harvests.index')->with('success', 'Data panen berhasil ditambah.');
    }

    // Update data panen
    public function update(Request $request, Harvest $harvest)
    {
        $validated = $request->validate([
            'harvest_date'    => 'required|date',
            'crop_type_id'    => 'required|exists:crop_types,id',
            'land_area_m2'    => 'required|numeric|min:0.01',
            'total_weight_kg' => 'required|numeric|min:0.01',
            'quality'         => 'nullable|in:A,B,C',
            'notes'           => 'nullable|string|max:255',
        ]);

        $harvest->update($validated);

        ActivityLogger::log('update', 'harvest', 'Update data panen: ID ' . $harvest->id);

        return redirect()->route('admin.harvests.index')->with('success', 'Data panen berhasil diupdate.');
    }

    // Hapus data panen
    public function destroy(Harvest $harvest)
    {
        $harvestId = $harvest->id;
        $harvest->delete();

        ActivityLogger::log('delete', 'harvest', 'Hapus data panen: ID ' . $harvestId);

        return redirect()->route('admin.harvests.index')->with('success', 'Data panen berhasil dihapus.');
    }
}
