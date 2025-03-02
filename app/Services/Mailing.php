<?php

namespace App\Services;

use Mailgun\Mailgun;


class Mailing
{

    protected $mail;

    public function __construct()
    {

        $this->mail = Mailgun::create('7a9c79a7741a230e56138fbb37dd22da-ac3d5f74-573ace38');
    }


    public function sendMessage($to, $order)
    {
        $userName = $order->user->name;
        $userLastName = $order->user->last_name;
        // Convertir los productos de la orden en una lista HTML
        $orders = '';
        foreach ($order ->items as $item) {
            $orders .= "<tr>
                                <td>{$item->name}</td>
                                <td>{$item->qty}</td>
                                <td>\${$item->price}</td>
                              </tr>";
        }
        

    // Generar el contenido del correo con los detalles de la orden
    $html = "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Orden Registrada</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        body {
            background-color: #f4f4f4;
            text-align: center;
            padding: 50px;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            margin-top: 30px; /* Asegura que el card esté visible con un margen arriba */
        }
        .card-body {
            padding: 20px;
        }
        .header {
            color: #007bff;
            font-size: 28px;
            margin-bottom: 20px;
        }
        .subheader {
            color: #007bff;
            font-size: 22px;
            margin-bottom: 20px;
        }
        .text {
            color: #333333;
            font-size: 16px;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .total {
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class='container'>
        <div class='card'>
            <div class='card-body text-center'>
                <h1 class='header'>¡Hola, {$userName} {$userLastName}!</h1>
                <h2 class='subheader'>Tu orden #{$order->id} fue registrada correctamente :)</h2>
                <p class='text'>En breves momentos se preparará tu orden.</p>

                <h3>Detalles de la orden:</h3>
                <table class='table'>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        {$orders}
                    </tbody>
                </table>

                <p class='total'>Total: \${$order->total}</p>
            </div>
        </div>
    </div>

    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js'></script>

</body>
</html>";


    




    // Enviar el correo
    $response = $this->mail->messages()->send('sanfelipedejesus.com', [
        'from'    => 'San Felipe de Jesus <store@sanfelipedejesus.com>',
        'to'      => $to,
        'subject' => 'Tu orden fue registrada!!',
        'html'    =>  $html,
    ]);

    error_log(json_encode($response));
}

}
