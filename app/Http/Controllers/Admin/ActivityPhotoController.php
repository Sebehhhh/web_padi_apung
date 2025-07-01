<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        ActivityPhoto::create([
            'activity_id' => $request->activity_id,
            'photo_url'   => $path,
            'caption'     => $request->caption,
            'taken_at'    => $request->taken_at,
        ]);

        return back()->with('success', 'Foto dokumentasi berhasil diupload.');
    }

    // Destroy: hapus foto
    public function destroy(ActivityPhoto $activity_photo)
    {
        if ($activity_photo->photo_url && Storage::disk('public')->exists($activity_photo->photo_url)) {
            Storage::disk('public')->delete($activity_photo->photo_url);
        }
        $activity_photo->delete();
        return back()->with('success', 'Foto dokumentasi berhasil dihapus.');
    }
}