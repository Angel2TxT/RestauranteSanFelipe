<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 0; 
            padding: 0; 
            display: flex; 
            flex-direction: column; 
            align-items: center; /* Centra el contenido horizontalmente */
            justify-content: center; /* Centra el contenido verticalmente */
            height: 100vh; /* Asegura que el contenido ocupe toda la altura */
            box-sizing: border-box;
        }

        .container {
            width: 90%; /* Ajusta el porcentaje según sea necesario */
            max-width: 1200px; /* Limita el tamaño máximo de la tabla */
            text-align: center;
            margin-top: 20px;
            overflow-x: auto; /* Permite el desplazamiento horizontal si la tabla es ancha */
        }

        .header img { 
            width: 120px; 
            height: auto; 
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-left: auto; 
            margin-right: auto; /* Centra la tabla horizontalmente */
            table-layout: fixed; /* Esto evitará que las celdas se estiren */
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center; /* Centra el contenido de la tabla */
            word-wrap: break-word; /* Rompe las palabras largas para evitar desbordamiento */
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #555;
        }

        .company-info {
            margin-top: 10px;
            text-align: center;
        }

        h2 {
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Encabezado con Logo -->
        <div class="header">
            <img src="{{ public_path('images/logoSFB.png') }}" alt="Logo de la Empresa">
            <h2>{{ $companyName }}</h2>
        </div>

        <!-- Información de la Empresa -->
        <div class="company-info">
            <p><strong>Dirección:</strong> {{ $companyAddress }}</p>
            <p><strong>Teléfono:</strong> {{ $companyPhone }}</p>
            <p><strong>Correo Electrónico:</strong> {{ $companyEmail }}</p>
        </div>

        <!-- Título del Reporte -->
        <h2>{{ $title }}</h2>

        <!-- Tabla de Datos -->
        <table>
            <thead>
                <tr>
                    @foreach ($columns as $column)
                        <th>{{ $column }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        @foreach ($row as $value)
                            <td>{{ $value }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pie de Página -->
    <div class="footer">
        <p>© {{ date('Y') }} {{ $companyName }} - Todos los derechos reservados.</p>
    </div>

</body>
</html>
