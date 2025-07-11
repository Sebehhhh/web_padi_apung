<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'task_name',
        'schedule_date',
        'start_time',
        'end_time',
        'description',
        'status',
        'priority',
        'created_by',
    ];

    /**
     * Get the user (pegawai) that this schedule belongs to.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the creator of this schedule.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
