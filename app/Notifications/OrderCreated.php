<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderCreated extends Notification
{
    use Queueable;

    public function __construct(public Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $typeLabel = match ($this->order->order_type) {
            'dine_in' => 'En mesa',
            'delivery' => 'A domicilio',
            'pickup' => 'Para llevar',
            default => $this->order->order_type,
        };

        $customer = $this->order->user?->name ?? 'Cliente';
        $total = number_format((float) $this->order->total, 2);

        return [
            'order_id' => $this->order->id,
            'title' => 'Nueva orden #' . $this->order->id,
            'body' => "{$customer} · {$typeLabel} · \${$total}",
            'url' => route('orders.show', $this->order),
            'type' => 'order_created',
        ];
    }
}
