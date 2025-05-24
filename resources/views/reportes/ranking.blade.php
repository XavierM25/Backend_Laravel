<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Ranking</title>
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
    <h2>Reporte de Ranking</h2>
    <table>
        <thead>
            <tr>
                <th>Posición</th>
                <th>Nombre</th>
                <th>Apellido Paterno</th>
                <th>Apellido Materno</th>
                <th>Categoría</th>
                <th>Subcategoría</th>
                <th>Puntos</th>
                <th>Estado</th>
                <th>Fecha de Actualización</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $item)
                <tr>
                    <td>{{ $loop->iteration}}</td>
                    <td>{{ $item->usuario->nombre }}</td>
                    <td>{{ $item->usuario->apellido_paterno }}</td>
                    <td>{{ $item->usuario->apellido_materno }}</td>
                    <td>{{ $item->subcategoria->categoria->nombre }}</td>
                    <td>{{ $item->subcategoria->nombre }}</td>
                    <td>{{ $item->puntos }}</td>
                    <td>{{ $item->estado }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->usuario->updated_at)->format('d/m/Y H:i:s') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
