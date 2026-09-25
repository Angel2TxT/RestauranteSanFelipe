<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['total', 'notes', 'status', 'fecha', 'user_id', 'order_type', 'delivery_address', 'table_id'];

    /**
     * Relación con los items de la orden.
     * La orden puede tener muchos items (muchos a muchos).
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_order')
                    ->withPivot(['qty', 'fecha']);
    }

    /**
     * Relación con la mesa asociada a la orden.
     * Cada orden está asociada a una mesa (si es tipo "dine_in").
     */
    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    /**
     * Relación con el usuario que realizó la orden.
     * Una orden pertenece a un único usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }
}
