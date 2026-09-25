<?php

namespace App\Http\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function generateOrderReport($orderId)
    {
        $order = Order::with(['user', 'table', 'items'])->find($orderId);

        if (!$order) {
            return redirect()->route('orders.index')->with('error', 'La orden no fue encontrada.');
        }

        $user = auth()->user();
        $isOwner = $user && (int) $user->id === (int) $order->user_id;
        $isStaff = $user && in_array((int) $user->role, [1, 2, 3], true);

        if (!$isOwner && !$isStaff) {
            abort(403);
        }

        return $this->streamOrderPdf($order, 'reporte_orden_' . $order->id . '.pdf');
    }

    public function streamOrderPdf(Order $order, string $filename = null)
    {
        $order->loadMissing(['user', 'table', 'items']);

        $imagePath = public_path('images/logoSFB.jpg');
        $imageSrc = '';
        if (file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $imageSrc = 'data:image/jpeg;base64,' . $imageData;
        }

        $html = $this->generateOrderHTML($order, $order->user, $imageSrc);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $pdf = new Dompdf($options);
        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        return $pdf->stream($filename ?: ('ticket_orden_' . $order->id . '.pdf'));
    }

    private function generateOrderHTML($order, $user, $imageSrc)
    {
        $customerName = trim((optional($user)->name ?? '') . ' ' . (optional($user)->last_name ?? ''));
        if ($customerName === '') {
            $customerName = 'Cliente';
        }

        $phone = optional($user)->phone ?? '—';
        $email = optional($user)->email ?? '—';
        $address = $order->delivery_address ?: (optional($user)->address ?? '—');
        $fecha = $order->fecha ?: now('America/Mexico_City')->toDateString();
        $hora = optional($order->created_at)->timezone('America/Mexico_City')->format('H:i') ?: '';
        $status = $this->getOrderStatusBadge($order->status);
        $type = $this->getOrderTypeBadge($order->order_type);
        $table = $this->getTableInfo($order);
        $notes = trim((string) ($order->notes ?? ''));
        $itemsCount = $order->items->sum(function ($item) {
            return (int) ($item->pivot->qty ?? $item->qty ?? 1);
        });

        $logoHtml = $imageSrc
            ? '<img src="' . $imageSrc . '" alt="Logo" class="logo">'
            : '<div class="logo-fallback">SF</div>';

        $metaTypeLine = $type;
        if ($order->order_type === 'dine_in' && $table !== 'No aplica') {
            $metaTypeLine .= ' · ' . e($table);
        }

        $notesHtml = $notes !== ''
            ? '<div class="notes"><strong>Notas del cliente</strong><p>' . e($notes) . '</p></div>'
            : '';

        return '
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
  @page { margin: 28px 32px; }
  body {
    font-family: DejaVu Sans, Arial, sans-serif;
    color: #1d1538;
    font-size: 12px;
    line-height: 1.45;
  }
  .ticket {
    border: 1px solid #e4dff0;
    border-radius: 12px;
    overflow: hidden;
  }
  .banner {
    background: #6046b6;
    color: #fff;
    padding: 22px 28px 18px;
  }
  .banner-table { width: 100%; border-collapse: collapse; }
  .banner-table td { vertical-align: middle; border: 0; padding: 0; }
  .logo {
    width: 58px;
    height: 58px;
    object-fit: cover;
    border-radius: 12px;
    background: #fff;
    border: 2px solid rgba(255,255,255,.35);
  }
  .logo-fallback {
    width: 58px;
    height: 58px;
    border-radius: 12px;
    background: #fff;
    color: #6046b6;
    text-align: center;
    line-height: 58px;
    font-weight: bold;
    font-size: 18px;
  }
  .brand { padding-left: 14px; }
  .brand h1 {
    margin: 0 0 2px;
    font-size: 20px;
    letter-spacing: .02em;
  }
  .brand p {
    margin: 0;
    font-size: 11px;
    opacity: .85;
  }
  .ticket-no {
    text-align: right;
  }
  .ticket-no .label {
    display: block;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: .12em;
    opacity: .8;
  }
  .ticket-no .value {
    display: block;
    font-size: 26px;
    font-weight: bold;
    line-height: 1.1;
  }
  .body { padding: 22px 28px 10px; }
  .meta {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 18px;
  }
  .meta td {
    width: 50%;
    vertical-align: top;
    padding: 0 10px 0 0;
    border: 0;
  }
  .meta td + td { padding: 0 0 0 10px; }
  .box {
    background: #f7f5fb;
    border: 1px solid #ebe7f5;
    border-radius: 10px;
    padding: 12px 14px;
  }
  .box .eyebrow {
    margin: 0 0 6px;
    font-size: 10px;
    font-weight: bold;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: #6046b6;
  }
  .box .name {
    margin: 0 0 4px;
    font-size: 14px;
    font-weight: bold;
    color: #151515;
  }
  .box p {
    margin: 2px 0;
    color: #5b5670;
    font-size: 11px;
  }
  .items {
    width: 100%;
    border-collapse: collapse;
    margin-top: 6px;
  }
  .items thead th {
    background: #1d1538;
    color: #fff;
    font-size: 10px;
    letter-spacing: .08em;
    text-transform: uppercase;
    padding: 10px 12px;
    border: 0;
  }
  .items thead th:first-child { border-radius: 8px 0 0 0; text-align: left; }
  .items thead th:last-child { border-radius: 0 8px 0 0; text-align: right; }
  .items thead th.center { text-align: center; }
  .items tbody td {
    padding: 11px 12px;
    border-bottom: 1px solid #efeaf8;
    color: #2c2544;
    font-size: 12px;
  }
  .items tbody td.center { text-align: center; }
  .items tbody td.right { text-align: right; }
  .items tbody tr:nth-child(even) td { background: #fbfafe; }
  .items .product { font-weight: bold; color: #151515; }
  .total-wrap {
    margin-top: 16px;
    background: linear-gradient(135deg, #6046b6, #4a348f);
    color: #fff;
    border-radius: 10px;
    padding: 14px 18px;
  }
  .total-table { width: 100%; border-collapse: collapse; }
  .total-table td { border: 0; padding: 0; vertical-align: middle; color: #fff; }
  .total-table .left {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: .08em;
    opacity: .9;
  }
  .total-table .right {
    text-align: right;
    font-size: 24px;
    font-weight: bold;
  }
  .notes {
    margin-top: 14px;
    padding: 12px 14px;
    border-left: 3px solid #6046b6;
    background: #faf9fc;
  }
  .notes strong {
    display: block;
    margin-bottom: 4px;
    font-size: 10px;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: #6046b6;
  }
  .notes p { margin: 0; color: #4b4560; font-size: 12px; }
  .footer {
    margin-top: 22px;
    padding: 16px 28px 22px;
    text-align: center;
    border-top: 1px dashed #ddd6ef;
  }
  .footer .thanks {
    margin: 0 0 4px;
    font-size: 15px;
    font-weight: bold;
    color: #6046b6;
  }
  .footer .contact {
    margin: 0;
    font-size: 10px;
    color: #7a748f;
  }
  .footer .tagline {
    margin: 8px 0 0;
    font-size: 10px;
    color: #9a94ad;
    letter-spacing: .04em;
  }
</style>
</head>
<body>
  <div class="ticket">
    <div class="banner">
      <table class="banner-table">
        <tr>
          <td style="width:70px;">' . $logoHtml . '</td>
          <td class="brand">
            <h1>Restaurante San Felipe</h1>
            <p>Tila, Chiapas · Tel. (919) 686-6720</p>
          </td>
          <td class="ticket-no">
            <span class="label">Ticket</span>
            <span class="value">#' . e((string) $order->id) . '</span>
          </td>
        </tr>
      </table>
    </div>

    <div class="body">
      <table class="meta">
        <tr>
          <td>
            <div class="box">
              <p class="eyebrow">Cliente</p>
              <p class="name">' . e($customerName) . '</p>
              <p>' . e($email) . '</p>
              <p>' . e($phone) . '</p>
              <p>' . e($address) . '</p>
            </div>
          </td>
          <td>
            <div class="box">
              <p class="eyebrow">Detalle del pedido</p>
              <p class="name">' . e($metaTypeLine) . '</p>
              <p>Fecha: ' . e($fecha) . ($hora ? ' · ' . e($hora) : '') . '</p>
              <p>Estado: ' . e($status) . '</p>
              <p>Productos: ' . (int) $itemsCount . '</p>
            </div>
          </td>
        </tr>
      </table>

      <table class="items">
        <thead>
          <tr>
            <th>Producto</th>
            <th class="center">Cant.</th>
            <th class="center">Precio</th>
            <th>Subtotal</th>
          </tr>
        </thead>
        <tbody>
          ' . $this->getOrderItemsRows($order) . '
        </tbody>
      </table>

      <div class="total-wrap">
        <table class="total-table">
          <tr>
            <td class="left">Total a pagar</td>
            <td class="right">$' . number_format((float) $order->total, 2) . '</td>
          </tr>
        </table>
      </div>

      ' . $notesHtml . '
    </div>

    <div class="footer">
      <p class="thanks">¡Gracias por su preferencia!</p>
      <p class="contact">info@sanfelipe.com · Hospedaje y Restaurante San Felipe de Jesús</p>
      <p class="tagline">Buen provecho</p>
    </div>
  </div>
</body>
</html>';
    }

    private function getOrderItemsRows($order)
    {
        $rows = '';
        $items = $order->items ?? collect();

        if ($items->isEmpty()) {
            return '<tr><td colspan="4" style="text-align:center;color:#7a748f;padding:18px;">Sin productos</td></tr>';
        }

        foreach ($items as $item) {
            $qty = (int) ($item->pivot->qty ?? $item->qty ?? 1);
            $subtotal = (float) $item->price * $qty;
            $rows .= '<tr>
                <td class="product">' . e($item->name) . '</td>
                <td class="center">' . $qty . '</td>
                <td class="center">$' . number_format((float) $item->price, 2) . '</td>
                <td class="right">$' . number_format($subtotal, 2) . '</td>
            </tr>';
        }

        return $rows;
    }

    private function getOrderStatusBadge($status)
    {
        return match ($status) {
            'pending' => 'Orden Pendiente',
            'in_progress' => 'En preparación',
            'ready_for_delivery' => 'Lista para entrega',
            'paid' => 'Pagada / por cobrar',
            'completed' => 'Completada',
            'cancelled_by_user' => 'Cancelada por el cliente',
            'cancelled_by_store' => 'Cancelada por el restaurante',
            default => $status,
        };
    }

    private function getOrderTypeBadge($type)
    {
        return match ($type) {
            'dine_in' => 'Local',
            'delivery' => 'Envío',
            'pickup' => 'Recoger',
            default => 'Desconocido',
        };
    }

    private function getTableInfo($order)
    {
        return $order->order_type == 'dine_in' ? optional($order->table)->name ?? 'No aplica' : 'No aplica';
    }


    public function generateUserReport($userId)
    {
        // Obtener el usuario por su ID
        $user = User::find($userId);

        // Verificar si el usuario existe
        if (!$user) {
            return redirect()->route('users.index')->with('error', 'El usuario no fue encontrado.');
        }

        // Crear el HTML para el reporte
        $imagePath = public_path('images/logoSFB.jpg');
        $imageSrc = file_exists($imagePath) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($imagePath)) : '';
        $html = $this->generateUserHTML($user, $imageSrc);

        // Configuración de DOMpdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        // Inicializar DOMpdf
        $pdf = new Dompdf($options);
        $pdf->loadHtml($html);

        // (Opcional) Configurar tamaño del papel y márgenes
        $pdf->setPaper('A4', 'portrait');

        // Renderizar el PDF
        $pdf->render();

        // Descargar el PDF
        return $pdf->stream('reporte_usuario_' . $user->id . '.pdf');
    }

    private function generateUserHTML($user, $imageSrc)
    {
        // Estilos para el reporte
        $html = '
    <style>
        /* Estilos generales */
        .header {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
        }

        /* Estilo de las filas */
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .logo {
            width: 200px;
            height: auto;
        }

        .title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            flex-grow: 1;
        }

        .contact-info {
            text-align: right;
            font-size: 16px;
            flex-grow: 1;
            padding-left: 20px;
            font-family: Arial, sans-serif;
        }

        .contact-info p {
            margin: 0;
        }

        .contact-info strong {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-family: Arial, sans-serif;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
        }
        th {
            background-color: rgb(109, 109, 109);
            color: white;
            text-transform: uppercase;
            font-weight: bold;
        }
        td {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        tr:nth-child(even) td {
            background-color: #f2f2f2;
        }
        tr:hover td {
            background-color: #eaeaea;
        }

        /* Estilo de las celdas */
        .cell-label {
            font-weight: bold;
            color: #333;
        }
        .cell-value {
            color: #555;
        }

        /* Estilo de la firma */
        .signature-container {
            margin-top: 50px;
            text-align: center;
            font-size: 16px;
        }
        .signature-line {
            margin-top: 50px;
            border-top: 1px solid black;
            width: 250px;
            margin-left: auto;
            margin-right: auto;
        }
        .signature-label {
            margin-top: 5px;
            font-weight: bold;
        }
    </style>

    <!-- Encabezado -->
    <div class="header">
        <!-- Fila del logo -->
        <div class="row">
            <img src="' . $imageSrc . '" alt="Logo" class="logo" />
        </div>
        <br>

        <!-- Fila del título -->
        <div class="row">
            <div class="title">Hospedaje San Felipe</div>
        </div>
        <br>

        <!-- Fila de los datos de contacto -->
        <div class="row">
            <div class="contact-info">
                <p><strong>CONTACTOS:</strong></p>
                <p><strong>Dirección:</strong> Calle Vicente Guerrero</p>
                <p><strong>Email:</strong> info@sanfelipe.com</p>
                <p><strong>Teléfono:</strong> (919) 6866720</p>
                <p><strong>Ciudad:</strong> Tila</p>
            </div>
        </div>
    </div>

    <!-- Encabezado del reporte -->
    <div class="header">
        <div class="title">Reporte de Usuario</div>
    </div>
    <br>

    <!-- Tabla de detalles de usuario -->
    <table>
        <thead>
            <tr>
                <th>Detalle</th>
                <th>Información</th>
            </tr>
        </thead>
        <tbody>
            
            <tr>
                <td><strong>Nombre</strong></td>
                <td>' . $user->name . '</td>
            </tr>
            <tr>
                <td><strong>Apellido</strong></td>
                <td>' . $user->last_name . '</td>
            </tr>
            <tr>
                <td><strong>Email</strong></td>
                <td>' . $user->email . '</td>
            </tr>
            <tr>
                <td><strong>Dirección</strong></td>
                <td>' . $user->address . '</td>
            </tr>
            <tr>
                <td><strong>Teléfono</strong></td>
                <td>' . $user->phone . '</td>
            </tr>
            <tr>
                <td><strong>Rol</strong></td>
                <td>' . ($user->role ? 'Admin' : 'Cliente') . '</td>
            </tr>
        </tbody>
    </table>

    <!-- Firma -->
    <div class="signature-container">
        <div class="signature-line"></div>
        <div class="signature-label">Firma del Responsable</div>
    </div>';

        return $html;
    }
}
