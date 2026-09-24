<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'address',
        'phone',
        'password',
        'role',
        'image',
    ];

    public function isAdmin()
    {
        return $this->role == 1;
    }

    public function isClient()
    {
        return $this->role == 0;
    }

    public function isEmployee()
    {
        return $this->role == 2;
    }

    public function isDelivery()
    {
        return $this->role == 3;
    }

    /**
     * Los atributos que deben ser ocultados para la serialización.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Obtener los atributos que deben ser casteados.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relación de uno a muchos con órdenes.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}

