<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Harvest;
use App\Models\CropType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HarvestController extends Controller
{
    /**
     * Tampilkan daftar hasil panen milik pegawai yang sedang login.
     * Filter sederhana: tanggal, jenis tanaman, kualitas.
     */
    public function index(Request $request)
    {
        $query = Harvest::with(['cropType'])
            ->where('user_id', Auth::id())
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

        $harvests = $query->paginate(10)->withQueryString();

        $cropTypes = CropType::where('is_active', 1)
            ->orderBy('name')
            ->get();

        return view('pegawai.harvests.index', compact('harvests', 'cropTypes'));
    }

    /**
     * Tampilkan form input hasil panen baru.
     */
    public function create()
    {
        $cropTypes = CropType::where('is_active', 1)
            ->orderBy('name')
            ->get();

        return view('pegawai.harvests.create', compact('cropTypes'));
    }

    /**
     * Simpan hasil panen baru milik pegawai sendiri.
     * Perhitungan otomatis tonase, produktivitas, kualitas.
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

        $data['user_id'] = Auth::id();

        Harvest::create($data);

        return redirect()
            ->route('pegawai.harvests.index')
            ->with('success', 'Data panen berhasil ditambah.');
    }

    /**
     * Tampilkan detail hasil panen milik pegawai sendiri.
     */
    public function show(Harvest $harvest)
    {
        // Pastikan hanya bisa melihat data sendiri
        if ($harvest->user_id !== Auth::id()) {
            abort(403);
        }
        return view('pegawai.harvests.show', compact('harvest'));
    }
}
