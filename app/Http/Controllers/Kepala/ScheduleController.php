<?php

namespace App\Http\Controllers\Kepala;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Display a listing of jadwal kerja harian (read-only).
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function index(Request $request)
    {
        $query = Schedule::with('user')
            ->orderByDesc('schedule_date')
            ->orderBy('start_time');

        // Filter: tanggal mulai
        if ($request->filled('start_date')) {
            $query->whereDate('schedule_date', '>=', $request->start_date);
        }

        // Filter: tanggal akhir
        if ($request->filled('end_date')) {
            $query->whereDate('schedule_date', '<=', $request->end_date);
        }

        // Filter: pegawai tertentu
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter: berdasarkan divisi
        if ($request->filled('division')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('division', $request->division);
            });
        }

        $schedules = $query
            ->paginate(10)
            ->withQueryString();

        // Untuk dropdown filter pegawai
        $users = User::where('role', 'pegawai')
            ->orderBy('name')
            ->get();

        // Untuk dropdown filter divisi
        $divisions = User::where('role', 'pegawai')
            ->distinct()
            ->orderBy('division')
            ->pluck('division')
            ->filter()
            ->values();

        return view('kepala.schedules.index', compact('schedules', 'users', 'divisions'));
    }

     public function export(Request $request)
    {
        $query = Schedule::with('user')
            ->orderByDesc('schedule_date')
            ->orderBy('start_time');

        // Filter: tanggal mulai
        if ($request->filled('start_date')) {
            $query->whereDate('schedule_date', '>=', $request->start_date);
        }

        // Filter: tanggal akhir
        if ($request->filled('end_date')) {
            $query->whereDate('schedule_date', '<=', $request->end_date);
        }

        // Filter: pegawai tertentu
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter: berdasarkan divisi
        if ($request->filled('division')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('division', $request->division);
            });
        }

        $schedules = $query->get();
        $filename = 'jadwal_kerja_' . now()->format('Ymd_His') . '.pdf';

        // generate PDF dari view khusus
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('kepala.schedules.export_pdf', compact('schedules'))
            ->setPaper('a4', 'potrait');

        return $pdf->download($filename);
    }

    /**
     * Create not available for Kepala.
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Store not available for Kepala.
     */
    public function store(Request $request)
    {
        abort(404);
    }

    /**
     * Show not available for Kepala.
     */
    public function show($id)
    {
        abort(404);
    }

    /**
     * Edit not available for Kepala.
     */
    public function edit($id)
    {
        abort(404);
    }

    /**
     * Update not available for Kepala.
     */
    public function update(Request $request, $id)
    {
        abort(404);
    }

    /**
     * Destroy not available for Kepala.
     */
    public function destroy($id)
    {
        abort(404);
    }
}
