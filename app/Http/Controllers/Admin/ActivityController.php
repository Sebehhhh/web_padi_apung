<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityLogger;

class ActivityController extends Controller
{
    // Tampilkan semua kegiatan
    public function index()
    {
        $activities = Activity::with(['category', 'photos'])->orderByDesc('activity_date')->paginate(10);
        $categories = ActivityCategory::where('is_active', 1)->orderBy('name')->get();
        return view('admin.activities.index', compact('activities', 'categories'));
    }

    // Simpan kegiatan baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'    => 'required|exists:activity_categories,id',
            'description'    => 'required|string|max:500',
            'location'       => 'nullable|string|max:100',
            'activity_date'  => 'required|date',
            'start_time'     => 'nullable',
            'end_time'       => 'nullable',
            'status'         => 'required|in:Draft,Pending,Selesai,Dibatalkan',
        ]);
        $validated['created_by'] = Auth::id();

        $activity = Activity::create($validated);

        // Logging
        ActivityLogger::log(
            'create',
            'activity',
            'Tambah kegiatan: ' . $activity->description . ' oleh user: ' . Auth::user()->name
        );

        return redirect()->route('admin.activities.index')->with('success', 'Kegiatan berhasil ditambah.');
    }

    // Update kegiatan
    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'category_id'    => 'required|exists:activity_categories,id',
            'description'    => 'required|string|max:500',
            'location'       => 'nullable|string|max:100',
            'activity_date'  => 'required|date',
            'start_time'     => 'nullable',
            'end_time'       => 'nullable',
            'status'         => 'required|in:Draft,Pending,Selesai,Dibatalkan',
        ]);
        $activity->update($validated);

        // Logging
        ActivityLogger::log(
            'update',
            'activity',
            'Update kegiatan: ' . $activity->description . ' oleh user: ' . Auth::user()->name
        );

        return redirect()->route('admin.activities.index')->with('success', 'Kegiatan berhasil diupdate.');
    }

    // Hapus kegiatan
    public function destroy(Activity $activity)
    {
        $desc = $activity->description;
        $activity->delete();

        // Logging
        ActivityLogger::log(
            'delete',
            'activity',
            'Hapus kegiatan: ' . $desc . ' oleh user: ' . Auth::user()->name
        );

        return redirect()->route('admin.activities.index')->with('success', 'Kegiatan berhasil dihapus.');
    }
}
