<?php

// app/Models/ActivityCategory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        // 'is_active',
    ];
}