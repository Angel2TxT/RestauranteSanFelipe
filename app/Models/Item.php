<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'price', 'qty', 'image', 'product_id', 'fecha'];

    /**
     * Relación con las órdenes.
     * Aquí especificamos la tabla pivote 'item_order' y las columnas adicionales que queremos obtener.
     */
    public function items()
{
    return $this->belongsToMany(Item::class, 'item_order')
                ->withPivot(['qty', 'fecha']);
}

    

}
