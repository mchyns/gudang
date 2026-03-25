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
        'phone',
        'address',
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
     * Check user role
     */
    public function hasRole($roles)
    {
        if (is_string($roles)) {
            return $this->role === $roles;
        }
        return in_array($this->role, $roles);
    }

    public function suppliedProducts()
    {
        return $this->hasMany(Product::class, 'supplier_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

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

