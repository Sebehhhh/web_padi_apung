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
    /**
     * Index: list data panen
     */
    public function index(Request $request)
    {
        // Base query: join user & cropType, urut terbaru
        $query = Harvest::with(['user', 'cropType'])
            ->orderByDesc('harvest_date');

        // Filter rentang tanggal
        if ($request->filled('date_start') && $request->filled('date_end')) {
            $query->whereBetween('harvest_date', [
                $request->date_start,
                $request->date_end,
            ]);
        } elseif ($request->filled('date_start')) {
            $query->where('harvest_date', '>=', $request->date_start);
        } elseif ($request->filled('date_end')) {
            $query->where('harvest_date', '<=', $request->date_end);
        }

        // Filter jenis tanaman
        if ($request->filled('crop_type_id')) {
            $query->where('crop_type_id', $request->crop_type_id);
        }

        // Filter kualitas
        if ($request->filled('quality')) {
            $query->where('quality', $request->quality);
        }

        // Paginate & pertahankan filter di query string
        $harvests = $query->paginate(10)->withQueryString();

        // Data untuk dropdown filter
        $cropTypes = CropType::where('is_active', 1)
            ->orderBy('name')
            ->get();

        return view('admin.harvests.index', compact('harvests', 'cropTypes'));
    }


    /**
     * Store: tambah data panen dengan perhitungan otomatis
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'harvest_date'    => 'required|date',
            'crop_type_id'    => 'required|exists:crop_types,id',
            'land_area_m2'    => 'required|numeric|min:0.01',
            'total_weight_kg' => 'required|numeric|min:0.01',
            'notes'           => 'nullable|string|max:255',
        ]);

        // Hitung tonase dan produktivitas
        $data['total_weight_ton']   = $data['total_weight_kg'] / 1000;
        $data['productivity_kg_m2'] = $data['total_weight_kg'] / $data['land_area_m2'];

        // Tentukan kualitas
        if ($data['productivity_kg_m2'] >= 0.6) {
            $data['quality'] = 'A';
        } elseif ($data['productivity_kg_m2'] >= 0.5) {
            $data['quality'] = 'B';
        } else {
            $data['quality'] = 'C';
        }

        // User pencatat
        $data['user_id'] = Auth::id();

        $harvest = Harvest::create($data);

        ActivityLogger::log(
            'create',
            'harvest',
            'Tambah data panen: ID ' . $harvest->id
        );

        return redirect()
            ->route('admin.harvests.index')
            ->with('success', 'Data panen berhasil ditambah.');
    }

    /**
     * Update data panen dengan perhitungan otomatis
     */
    public function update(Request $request, Harvest $harvest)
    {
        $data = $request->validate([
            'harvest_date'    => 'required|date',
            'crop_type_id'    => 'required|exists:crop_types,id',
            'land_area_m2'    => 'required|numeric|min:0.01',
            'total_weight_kg' => 'required|numeric|min:0.01',
            'notes'           => 'nullable|string|max:255',
        ]);

        // Hitung ulang
        $data['total_weight_ton']   = $data['total_weight_kg'] / 1000;
        $data['productivity_kg_m2'] = $data['total_weight_kg'] / $data['land_area_m2'];

        // Tentukan kualitas
        if ($data['productivity_kg_m2'] >= 0.6) {
            $data['quality'] = 'A';
        } elseif ($data['productivity_kg_m2'] >= 0.5) {
            $data['quality'] = 'B';
        } else {
            $data['quality'] = 'C';
        }

        $harvest->update($data);

        ActivityLogger::log(
            'update',
            'harvest',
            'Update data panen: ID ' . $harvest->id
        );

        return redirect()
            ->route('admin.harvests.index')
            ->with('success', 'Data panen berhasil diupdate.');
    }

    /**
     * Hapus data panen
     */
    public function destroy(Harvest $harvest)
    {
        $harvestId = $harvest->id;
        $harvest->delete();

        ActivityLogger::log(
            'delete',
            'harvest',
            'Hapus data panen: ID ' . $harvestId
        );

        return redirect()
            ->route('admin.harvests.index')
            ->with('success', 'Data panen berhasil dihapus.');
    }
}
