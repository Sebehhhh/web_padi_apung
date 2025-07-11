<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    /**
     * Tampilkan daftar jadwal milik pegawai yang sedang login.
     * Bisa filter tanggal.
     */
    public function index(Request $request)
    {
        $query = Schedule::where('user_id', Auth::id())
            ->orderByDesc('schedule_date')
            ->orderBy('start_time');

        // Filter tanggal (opsional)
        if ($request->filled('start_date')) {
            $query->whereDate('schedule_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('schedule_date', '<=', $request->end_date);
        }

        $schedules = $query->paginate(10)->appends($request->except('page'));

        return view('pegawai.schedules.index', compact('schedules'));
    }

    /**
     * Tandai jadwal sebagai "Selesai" jika diizinkan.
     */
    public function complete($id)
    {
        $schedule = Schedule::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Hanya bisa menandai jika status masih Pending
        if ($schedule->status !== 'Pending') {
            return redirect()->back()->with('error', 'Jadwal sudah selesai atau tidak dapat diubah.');
        }

        $schedule->status = 'Selesai';
        $schedule->save();

        return redirect()->back()->with('success', 'Jadwal berhasil ditandai sebagai selesai.');
    }
}
