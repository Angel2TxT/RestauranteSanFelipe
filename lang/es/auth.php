<?php

return [

    'required' => 'El campo :attribute es obligatorio.',
    'email' => 'El campo :attribute debe ser una dirección de correo válida.',
    'max' => [
        'string' => 'El campo :attribute no debe ser mayor que :max caracteres.',
    ],
    'min' => [
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'confirmed' => 'La confirmación de la contraseña no coincide.',
    // Agrega aquí más reglas de validación que necesites
    [
        'failed' => 'Estas credenciales no coinciden con nuestros registros.',
        'throttle' => 'Demasiados intentos de inicio de sesión. Por favor, intente de nuevo en :seconds segundos.',
     ],
];
