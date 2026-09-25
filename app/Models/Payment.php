<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'cashier_id',
        'amount_due',
        'amount_received',
        'change_given',
        'method',
        'notes',
    ];

    protected $casts = [
        'amount_due' => 'decimal:2',
        'amount_received' => 'decimal:2',
        'change_given' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }
}
