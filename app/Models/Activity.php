<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'description',
        'location',
        'activity_date',
        'start_time',
        'end_time',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'rejected_reason',
    ];

    public function photos() {
        return $this->hasMany(ActivityPhoto::class);
    }

    // Relasi ke kategori
    public function category()
    {
        return $this->belongsTo(ActivityCategory::class, 'category_id');
    }

    // Relasi ke user yang membuat
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke user yang meng-approve
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}