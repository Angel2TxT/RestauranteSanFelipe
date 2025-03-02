<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['total', 'notes', 'status', 'fecha', 'user_id', 'order_type', 'address', 'table_id'];


    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_order')
                    ->withPivot(['qty', 'fecha']);
    }
    


        public function table()
    {
        return $this->belongsTo(Table::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
