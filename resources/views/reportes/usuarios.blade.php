<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Usuarios</title>
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
    <h2>Reporte de Usuarios</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido Paterno</th>
                <th>Apellido Materno</th>
                <th>Sexo</th>
                <th>Username</th>
                <th>Email</th>
                <th>Fecha de Nacimiento</th>
                <th>Campus</th>
                <th>Carrera</th>
                <th>Número de Teléfono</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $usuario)
                <tr>
                    <td>{{ $usuario->id }}</td>
                    <td>{{ $usuario->nombre }}</td>
                    <td>{{ $usuario->apellido_paterno }}</td>
                    <td>{{ $usuario->apellido_materno }}</td>
                    <td>{{ $usuario->sexo }}</td>
                    <td>{{ $usuario->username }}</td>
                    <td>{{ $usuario->email }}</td>
                    <td>{{ \Carbon\Carbon::parse($usuario->fecha_nacimiento)->format('d/m/Y') }}</td>
                    <td>{{ $usuario->campus }}</td>
                    <td>{{ $usuario->carrera }}</td>
                    <td>{{ $usuario->numero_telefono }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
