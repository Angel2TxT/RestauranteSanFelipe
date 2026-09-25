<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public Order $order,
        public ?string $previousStatus = null
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public static function statusLabel(?string $status): string
    {
        return match ($status) {
            'pending' => 'Pendiente',
            'in_progress' => 'En preparación',
            'ready_for_delivery' => 'Lista',
            'paid' => 'Por pagar',
            'completed' => 'Completada',
            default => $status ?? 'Actualizada',
        };
    }

    public function toArray(object $notifiable): array
    {
        $label = self::statusLabel($this->order->status);
        $hint = match ($this->order->status) {
            'pending' => 'Recibimos tu pedido',
            'in_progress' => 'Estamos cocinando tu pedido',
            'ready_for_delivery' => 'Tu orden ya está lista',
            'paid' => 'Procede a pagar tu orden',
            'completed' => 'Tu pedido fue completado',
            default => 'El estado de tu orden cambió',
        };

        return [
            'order_id' => $this->order->id,
            'title' => 'Orden #' . $this->order->id . ': ' . $label,
            'body' => $hint,
            'url' => route('orders.my'),
            'type' => 'order_status',
            'status' => $this->order->status,
            'previous_status' => $this->previousStatus,
        ];
    }
}
