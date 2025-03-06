<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class NotificationController extends Controller
{
    public function sendNotification()
    {
        // Obtiene el usuario autenticado
        $user = auth()->user();

        if ($user) {
            // Llama al método que envía la notificación
            $user->sendCustomNotification();
            return response()->json(['message' => 'Notificación enviada']);
        } else {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }
    }
}
