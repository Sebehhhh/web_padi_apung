<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id', 'item_name', 'item_type', 'quantity', 'unit'
    ];

    public function request()
    {
        return $this->belongsTo(Request::class);
    }
}