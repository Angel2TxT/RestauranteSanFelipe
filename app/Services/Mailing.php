<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Mailgun\Mailgun;

class Mailing
{
    protected ?Mailgun $mail = null;

    public function __construct()
    {
        $apiKey = config('services.mailgun.secret') ?: env('MAILGUN_SECRET');

        if (!empty($apiKey)) {
            $this->mail = Mailgun::create($apiKey);
        }
    }

    public function sendMessage($to, $order): void
    {
        if (!$this->mail) {
            Log::info('Correo de orden omitido: MAILGUN_SECRET no configurado.', [
                'to' => $to,
                'order_id' => $order->id ?? null,
            ]);
            return;
        }

        $order->loadMissing(['user', 'items']);

        $userName = $order->user->name ?? 'Cliente';
        $userLastName = $order->user->last_name ?? '';

        $ordersHtml = '';
        foreach ($order->items as $item) {
            $qty = $item->pivot->qty ?? $item->qty ?? 1;
            $ordersHtml .= "<tr>
                <td>{$item->name}</td>
                <td>{$qty}</td>
                <td>\${$item->price}</td>
            </tr>";
        }

        $html = "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Orden Registrada</title>
</head>
<body style='font-family: Arial, sans-serif; background:#f4f4f4; padding:30px;'>
    <div style='max-width:700px;margin:0 auto;background:#fff;padding:24px;border-radius:8px;'>
        <h1>¡Hola, {$userName} {$userLastName}!</h1>
        <h2>Tu orden #{$order->id} fue registrada correctamente</h2>
        <p>En breves momentos se preparará tu orden.</p>
        <table style='width:100%;border-collapse:collapse;'>
            <thead>
                <tr>
                    <th align='left'>Producto</th>
                    <th align='left'>Cantidad</th>
                    <th align='left'>Precio</th>
                </tr>
            </thead>
            <tbody>{$ordersHtml}</tbody>
        </table>
        <p><strong>Total: \${$order->total}</strong></p>
    </div>
</body>
</html>";

        $domain = config('services.mailgun.domain') ?: env('MAILGUN_DOMAIN', 'sanfelipedejesus.com');
        $from = config('services.mailgun.from') ?: env('MAILGUN_FROM', 'San Felipe de Jesus <store@sanfelipedejesus.com>');

        try {
            $this->mail->messages()->send($domain, [
                'from' => $from,
                'to' => $to,
                'subject' => 'Tu orden fue registrada',
                'html' => $html,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al enviar correo de orden: ' . $e->getMessage(), [
                'order_id' => $order->id ?? null,
                'to' => $to,
            ]);
        }
    }
}
