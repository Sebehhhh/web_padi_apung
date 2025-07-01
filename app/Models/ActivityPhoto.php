<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'photo_url',
        'caption',
        'taken_at',
        // 'uploaded_by', // Kalau nanti ingin simpan user yang upload
    ];

    // Relasi ke aktivitas
    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    // Kalau nanti pakai, relasi ke user uploader
    // public function uploader()
    // {
    //     return $this->belongsTo(User::class, 'uploaded_by');
    // }
}