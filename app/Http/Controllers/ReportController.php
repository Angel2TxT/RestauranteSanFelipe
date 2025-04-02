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
        // Obtener la orden por su ID
        $order = Order::with('user', 'table')->find($orderId);

        // Verificar si la orden existe
        if (!$order) {
            return redirect()->route('orders.index')->with('error', 'La orden no fue encontrada.');
        }

        // Ruta al archivo de la imagen
        $imagePath = public_path('images/logoSFB.jpg');

        // Verificar si la imagen existe en la ruta
        if (file_exists($imagePath)) {
            // Convertir la imagen a base64
            $imageData = base64_encode(file_get_contents($imagePath));
            $imageSrc = 'data:image/jpeg;base64,' . $imageData; // Base64 data URL
        } else {
            // Si la imagen no existe, usar una imagen predeterminada
            $imageSrc = ''; // Puedes usar una URL de una imagen predeterminada
        }

        // Crear el HTML para el reporte
        $html = $this->generateOrderHTML($order, $order->user, $imageSrc);

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
        return $pdf->stream('reporte_orden_' . $order->id . '.pdf');
    }

    private function generateOrderHTML($order, $user, $imageSrc)
    {
        // Generar el HTML del reporte con el encabezado y la tabla
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
            background-color:rgb(109, 109, 109);
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
        <div class="row">
            <img src="' . $imageSrc . '" alt="Logo" class="logo" />
        </div>
    </div>

                <!-- Fila del título -->
                <div class="row">
                    <div class="title">Hospedaje San Felipe</div>
                </div>

                <!-- Fila de los datos de contacto -->
                <div class="row">
                    <div class="contact-info">
                        <p><strong>Dirección:</strong> Calle Ejemplo 123</p>
                        <p><strong>Email:</strong> info@sanfelipe.com</p>
                        <p><strong>Teléfono:</strong> (919) 6866720</p>
                        <p><strong>Ciudad:</strong> Tila</p>
                        
                    </div>
                </div>
            </div>

            <!-- Título del reporte -->
            <div class="header">
                
            <h1 style="text-align: center;">Reporte de Orden #' . $order->id . '</h1>
        </div>
            <br>
    <!-- Detalles del usuario -->
    <p><strong>DATOS DEL USUARIO: </strong></p>
    <p><strong>Nombre:</strong> ' . $user->name . ' ' . $user->last_name . '</p>
    <p><strong>Email:</strong> ' . $user->email . '</p>
    <p><strong>Dirección:</strong> ' . $user->address . '</p>
    <p><strong>Teléfono:</strong> ' . $user->phone . '</p>
    <br>


            <!-- Tabla de detalles de la orden -->
            <table>
        <thead>
            <tr>
                <th>Detalle</th>
                <th>Información</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="cell-label"><strong>Cliente</strong></td>
                <td class="cell-value">' . $order->user->name . '</td>
            </tr>
            <tr>
                <td class="cell-label"><strong>Estado</strong></td>
                <td class="cell-value">' . $this->getOrderStatusBadge($order->status) . '</td>
            </tr>
            <tr>
                <td class="cell-label"><strong>Mesa</strong></td>
                <td class="cell-value">' . $this->getTableInfo($order) . '</td>
            </tr>
            <tr>
                <td class="cell-label"><strong>Tipo de Orden</strong></td>
                <td class="cell-value">' . $this->getOrderTypeBadge($order->order_type) . '</td>
            </tr>
            <tr>
                <td class="cell-label"><strong>Total</strong></td>
                <td class="cell-value">' . number_format($order->total, 2) . '</td>
            </tr>
        </tbody>
    </table>

    <!-- Firma -->
    <div class="signature-container">
        <div class="signature-line"></div>
        <div class="signature-label">Firma del Responsable</div>
    </div>
        ';

        return $html;
    }

    private function getOrderStatusBadge($status)
    {
        return match ($status) {
            'pending' => 'Orden Pendiente',
            'in_progress' => 'Orden en Proceso',
            'ready_for_delivery' => 'Enviado',
            'completed' => 'Completado',
            default => 'Desconocido',
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
