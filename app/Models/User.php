<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nip_nik',
        'name',
        'position',
        'division',
        'address',
        'role',
        'photo_url',
        'email',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Contoh relasi ke activity (bisa dikembangkan sesuai kebutuhan)
    // public function activities()
    // {
    //     return $this->hasMany(Activity::class, 'created_by');
    // }

    // public function approvedActivities()
    // {
    //     return $this->hasMany(Activity::class, 'approved_by');
    // }

    // ...tambahkan relasi lain sesuai kebutuhan modul lain
}