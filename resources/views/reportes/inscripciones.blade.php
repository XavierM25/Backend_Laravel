<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Inscripciones</title>
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
    <h2>Reporte de Inscripciones</h2>
    <table>
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Categoría</th>
                <th>Subcategoría</th>
                <th>Horario</th>
                <th>Estado</th>
                <th>N° Vacante</th>
                <th>Comentarios</th>
                <th>Fecha de Inscripción</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $inscripcion)
                <tr>
                    <td>{{ $inscripcion->usuario->nombre }}</td>
                    <td>{{ $inscripcion->categoria->nombre }}</td>
                    <td>{{ $inscripcion->subcategoria->nombre }}</td>
                    <td>{{ $inscripcion->horario->dia }} - {{ $inscripcion->horario->hora_inicio }} - {{ $inscripcion->horario->hora_fin }}</td>
                    <td>{{ $inscripcion->estado }}</td>
                    <td>{{ $inscripcion->numero_vacante }}</td>
                    <td>{{ $inscripcion->comentarios}}</td>
                    <td>{{ \Carbon\Carbon::parse($inscripcion->created_at)->format('d/m/Y H:i:s') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
