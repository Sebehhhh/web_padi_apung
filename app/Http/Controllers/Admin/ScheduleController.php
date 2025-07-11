<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    /**
     * Tampilkan semua jadwal kerja.
     */
    public function index(Request $request)
    {
        $query = Schedule::with('user')->orderByDesc('schedule_date')->orderBy('start_time');

        // Filter tanggal range (opsional)
        if ($request->filled('start_date')) {
            $query->whereDate('schedule_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('schedule_date', '<=', $request->end_date);
        }
        // Filter pegawai
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $schedules = $query->paginate(10)->appends($request->except('page'));
        $users = User::where('role', 'pegawai')->orderBy('name')->get();

        return view('admin.schedules.index', compact('schedules', 'users'));
    }

    /**
     * Simpan jadwal baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'       => 'required|exists:users,id',
            'task_name'     => 'required|string|max:255',
            'schedule_date' => 'required|date',
            'start_time'    => 'nullable|date_format:H:i',
            'end_time'      => 'nullable|date_format:H:i|after_or_equal:start_time',
            'description'   => 'nullable|string|max:1000',
            'status'        => 'required|in:Pending,Selesai',
            'priority'      => 'nullable|in:Low,Medium,High',
        ]);

        $validated['created_by'] = Auth::id();

        $sched = Schedule::create($validated);

        ActivityLogger::log(
            'create',
            'schedule',
            "Tambah jadwal ID {$sched->id} ({$sched->task_name}) oleh " . Auth::user()->name
        );

        return redirect()
            ->route('admin.schedules.index')
            ->with('success', 'Jadwal berhasil ditambah.');
    }

    /**
     * Update jadwal.
     */
    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'user_id'       => 'required|exists:users,id',
            'task_name'     => 'required|string|max:255',
            'schedule_date' => 'required|date',
            'start_time' => ['nullable', 'date_format:H:i:s'],
            'end_time'   => ['nullable', 'date_format:H:i:s', 'after_or_equal:start_time'],
            'description'   => 'nullable|string|max:1000',
            'status'        => 'required|in:Pending,Selesai',
            'priority'      => 'nullable|in:Low,Medium,High',
        ], [
            'start_time.regex' => 'Format jam mulai harus H:i (contoh: 08:00).',
            'start_time.date_format' => 'Format jam mulai harus H:i (contoh: 08:00).',
            'end_time.regex' => 'Format jam selesai harus H:i (contoh: 17:00).',
            'end_time.date_format' => 'Format jam selesai harus H:i (contoh: 17:00).',
        ]);

        $schedule->update($validated);

        ActivityLogger::log(
            'update',
            'schedule',
            "Update jadwal ID {$schedule->id} oleh " . Auth::user()->name
        );

        return redirect()
            ->route('admin.schedules.index')
            ->with('success', 'Jadwal berhasil diupdate.');
    }

    /**
     * Hapus jadwal.
     */
    public function destroy(Schedule $schedule)
    {
        $id = $schedule->id;
        $schedule->delete();

        ActivityLogger::log(
            'delete',
            'schedule',
            "Hapus jadwal ID {$id} oleh " . Auth::user()->name
        );

        return redirect()
            ->route('admin.schedules.index')
            ->with('success', 'Jadwal berhasil dihapus.');
    }
}
