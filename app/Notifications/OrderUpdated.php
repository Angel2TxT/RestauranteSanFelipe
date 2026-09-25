<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderUpdated extends Notification
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
        $customer = $this->order->user?->name ?? 'Cliente';
        $total = number_format((float) $this->order->total, 2);

        return [
            'order_id' => $this->order->id,
            'title' => 'Orden #' . $this->order->id . ' actualizada',
            'body' => "{$customer} modificó su pedido · \${$total}",
            'url' => route('orders.show', $this->order),
            'type' => 'order_updated',
        ];
    }
}
