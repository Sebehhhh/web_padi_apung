<?php

namespace App\Http\Controllers\Kepala;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityCategory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class ActivityController extends Controller
{
    /**
     * Display a listing of kegiatan lapangan (read-only).
     *
     * View menerima:
     * - $activities  : paginasi list activities (with category & photos)
     * - $categories  : list kategori aktif untuk filter dropdown
     */
    public function index(Request $request)
    {
        $query = Activity::with(['category', 'photos'])
            ->orderByDesc('activity_date')
            ->orderByDesc('start_time');  // gunakan start_time, bukan activity_time

        // Filter by start date
        if ($request->filled('date_start')) {
            $query->whereDate('activity_date', '>=', $request->date_start);
        }
        // Filter by end date
        if ($request->filled('date_end')) {
            $query->whereDate('activity_date', '<=', $request->date_end);
        }
        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $activities = $query
            ->paginate(10)
            ->withQueryString();

        $categories = ActivityCategory::where('is_active', 1)
            ->orderBy('name')
            ->get();

        return view('kepala.activities.index', compact('activities', 'categories'));
    }

    public function export(Request $request): Response
    {
        // ulang filter
        $query = Activity::with('category')
            ->orderByDesc('activity_date')
            ->orderBy('start_time');

        if ($request->filled('date_start')) {
            $query->whereDate('activity_date', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('activity_date', '<=', $request->date_end);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $activities = $query->get();
        $filename   = 'kegiatan_' . now()->format('Ymd_His') . '.pdf';

        // generate PDF dari view khusus
        $pdf = Pdf::loadView('kepala.activities.export_pdf', compact('activities'))
            ->setPaper('a4', 'potrait');

        return $pdf->download($filename);
    }
}
