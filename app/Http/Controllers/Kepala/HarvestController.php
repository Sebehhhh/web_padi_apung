<?php

namespace App\Http\Controllers\Kepala;

use App\Http\Controllers\Controller;
use App\Models\Harvest;
use App\Models\CropType;
use Illuminate\Http\Request;

class HarvestController extends Controller
{
    /**
     * Tampilkan daftar hasil panen tim (read-only).
     *
     * Variabel ke view:
     * - $harvests   : paginasi hasil panen (dengan filter)
     * - $cropTypes  : daftar crop types untuk dropdown filter
     */
    public function index(Request $request)
    {
        $query = Harvest::with('cropType')
            ->orderByDesc('harvest_date');

        // filter rentang tanggal panen
        if ($request->filled('date_start')) {
            $query->whereDate('harvest_date', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('harvest_date', '<=', $request->date_end);
        }

        // filter jenis tanaman
        if ($request->filled('crop_type_id')) {
            $query->where('crop_type_id', $request->crop_type_id);
        }

        // filter kualitas
        if ($request->filled('quality')) {
            $query->where('quality', $request->quality);
        }

        $harvests = $query
            ->paginate(10)
            ->withQueryString();

        $cropTypes = CropType::where('is_active', 1)
            ->orderBy('name')
            ->get();

        return view('kepala.harvests.index', compact('harvests', 'cropTypes'));
    }

    public function export(Request $request)
    {
        $query = Harvest::with(['user', 'cropType']);

        // filter rentang tanggal panen
        if ($request->filled('date_start')) {
            $query->whereDate('harvest_date', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('harvest_date', '<=', $request->date_end);
        }

        // filter jenis tanaman
        if ($request->filled('crop_type_id')) {
            $query->where('crop_type_id', $request->crop_type_id);
        }

        // filter kualitas
        if ($request->filled('quality')) {
            $query->where('quality', $request->quality);
        }

        $harvests = $query->orderByDesc('harvest_date')->get();
        $filename = 'hasil-panen-' . now()->format('Ymd_His') . '.pdf';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('kepala.harvests.export_pdf', compact('harvests'))
            ->setPaper('a4', 'potrait');

        return $pdf->download($filename);
    }

    // -----------------------
    // Method lain tidak digunakan
    // -----------------------

    public function create()
    {
        abort(404);
    }

    public function store(Request $request)
    {
        abort(404);
    }

    public function show($id)
    {
        abort(404);
    }

    public function edit($id)
    {
        abort(404);
    }

    public function update(Request $request, $id)
    {
        abort(404);
    }

    public function destroy($id)
    {
        abort(404);
    }
}
