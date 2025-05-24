<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Actividades</title>
    <style>
        table {
            width: auto;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h2>Reporte de Actividades</h2>
    <table>
        <thead>
            <tr>
                <th>Categoría</th>
                <th>Subcategoría</th>
                <th>Horarios</th>
                <th>Vacantes</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $actividad)
                <tr>
                    <td>{{ $actividad->categoria->nombre }}</td>
                    <td>{{ $actividad->nombre }}</td>
                    <td>
                        <ul>
                            @foreach ($actividad->horarios as $horario)
                                <li>{{ $horario->dia }} - {{ $horario->hora_inicio }} - {{ $horario->hora_fin }}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td>{{ $actividad->vacantes }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
