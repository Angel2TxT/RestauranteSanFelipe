<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Symfony\Component\HttpKernel\Profiler\Profile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\YourNotification; // Asegúrate de que esta línea esté presente


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
        'last_name',  // Agregar apellido
        'email',
        'address',    // Agregar dirección
        'phone',      // Agregar teléfono
        'password',
    ];


    public function isAdmin() {
        return $this->role == 1;
    }

    public function isClient() {
        return $this->role == 0;
    }

    public function isEmployee() {
        return $this->role == 2;
    }

    public function isDelivery() {
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
     * Un usuario puede tener muchas órdenes.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relación con el perfil del usuario.
     * Un usuario tiene un perfil (si lo necesitas).
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Relación de notificaciones.
     * Un usuario puede recibir muchas notificaciones.
     */
    


    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    /**
     * Método para enviar una notificación personalizada.
     * Esto es opcional, puedes enviar notificaciones directamente desde tu controlador o evento.
     */
    
    /**
     * Método para enviar una notificación personalizada.
     * Esto es opcional, puedes enviar notificaciones directamente desde tu controlador o evento.
     */
   
}
