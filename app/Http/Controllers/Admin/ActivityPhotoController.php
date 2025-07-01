<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ActivityLogger;

class ActivityPhotoController extends Controller
{
    // Store: upload foto dokumentasi
    public function store(Request $request)
    {
        $validated = $request->validate([
            'activity_id' => 'required|exists:activities,id',
            'photo'       => 'required|image|max:2048', // 2MB
            'caption'     => 'nullable|string|max:100',
            'taken_at'    => 'nullable|date',
        ]);

        // Batasi maksimal 5 foto per activity_id
        $currentCount = ActivityPhoto::where('activity_id', $request->activity_id)->count();
        if ($currentCount >= 5) {
            return back()->with('error', 'Maksimal 5 foto per kegiatan.');
        }

        $file = $request->file('photo');
        $path = $file->store('activity_photos', 'public');

        $photo = ActivityPhoto::create([
            'activity_id' => $request->activity_id,
            'photo_url'   => $path,
            'caption'     => $request->caption,
            'taken_at'    => $request->taken_at,
        ]);

        // Logging
        ActivityLogger::log(
            'create',
            'activity_photo',
            'Upload foto dokumentasi kegiatan ID: ' . $request->activity_id . ' oleh user: ' . Auth::user()->name
        );

        return back()->with('success', 'Foto dokumentasi berhasil diupload.');
    }

    // Destroy: hapus foto
    public function destroy(ActivityPhoto $activity_photo)
    {
        $desc = $activity_photo->caption ?? $activity_photo->photo_url;

        if ($activity_photo->photo_url && Storage::disk('public')->exists($activity_photo->photo_url)) {
            Storage::disk('public')->delete($activity_photo->photo_url);
        }
        $activity_photo->delete();

        // Logging
        ActivityLogger::log(
            'delete',
            'activity_photo',
            'Hapus foto dokumentasi (ID: ' . $activity_photo->id . ', Caption: ' . $desc . ') oleh user: ' . Auth::user()->name
        );

        return back()->with('success', 'Foto dokumentasi berhasil dihapus.');
    }
}
