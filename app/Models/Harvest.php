<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Harvest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'crop_type_id',
        'harvest_date',
        'land_area_m2',
        'total_weight_kg',
        'quality',
        'notes'
    ];

    // Relasi: user yang mencatat panen
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: jenis tanaman
    public function cropType()
    {
        return $this->belongsTo(CropType::class, 'crop_type_id');
    }
}