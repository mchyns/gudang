<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Kolom yang boleh diisi (Mass Assignment)
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Pastikan ini sudah ada untuk dropdown nanti!
    ];

    /**
     * Kolom yang disembunyikan saat data dipanggil (Keamanan)
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting data (misal password otomatis di-hash)
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

