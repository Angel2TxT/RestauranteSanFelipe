<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    private $companyInfo = [
        'companyName' => 'San Felipe de Jesus S.A.',
        'companyAddress' => 'Segunda sur Ote. #SN, Tila',
        'companyPhone' => '(919) 686/6720',
        'companyEmail' => 'sanfelipedejesus.com',
    ];

    public function index()
    {
        return view('reports.index'); // Vista para seleccionar reporte
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

    private function getTableInfo($order)
    {
        return $order->order_type == 'dine_in' ? optional($order->table)->name ?? 'No aplica' : 'No aplica';
    }

    public function generatePDF(Request $request, $type)
{
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    switch ($type) {
        case 'orders':
            $title = 'Reporte de Órdenes';
            $data = Order::with('user', 'table')
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    return $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->get()
                ->map(function ($order) {
                    return [
                        'ID' => $order->id,
                        'Cliente' => optional($order->user)->name ?? 'Sin Cliente',
                        'Total' => number_format($order->total, 2) . ' USD',
                        'Estatus' => $this->getOrderStatusBadge($order->status),
                        'Tipo' => $this->getOrderTypeBadge($order->order_type),
                        'Mesa' => $this->getTableInfo($order),
                        'Fecha' => $order->created_at->format('d-m-Y'),
                    ];
                });
            break;

        case 'users':
            $title = 'Reporte de Usuarios';
            $data = User::all()->map(function ($user) {
                return [
                    'ID' => $user->id,
                    'Nombre' => $user->name,
                    'Apellido' => $user->last_name ?? 'No registrado',
                    'Email' => $user->email,
                    'Dirección' => $user->address ?? 'No registrada',
                    'Teléfono' => $user->phone ?? 'No registrado',
                    'Usuario' => $user->admin ? 'Admin' : 'Cliente',
                    'Fecha de Registro' => $user->created_at->format('d-m-Y'),
                ];
            });
            break;

        case 'products':
            $title = 'Reporte de Productos';
            $data = Product::with('category')->get()->map(function ($product) {
                return [
                    'ID' => $product->id,
                    'Nombre' => $product->name,
                    'Precio' => number_format($product->price, 2) . ' mx',
                    'Categoría' => optional($product->category)->name ?? 'Sin Categoría',
                    'Descripción' => $product->description ?? 'No disponible',
                ];
            });
            break;
    }

    // Evitar error si no hay datos
    $columns = array_keys($data->first() ?? []);

    // Cargar la vista con los datos y la información de la empresa
    $pdf = Pdf::loadView('reports.generic-pdf', array_merge([
        'title' => $title,
        'columns' => $columns,
        'data' => $data,
    ], $this->companyInfo));

    return $pdf->download(strtolower(str_replace(' ', '_', $title)) . '.pdf');
}

}
