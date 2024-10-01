<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Curso</title>
</head>
<body>
    <h2>Registrar Certificado</h2>
    <form action="guardar_datos.php" method="post">
        Matrícula: <input type="text" name="matricula" required><br>
        Nombres: <input type="text" name="nombres" required><br>
        Apellido Paterno: <input type="text" name="apellido_paterno" required><br>
        Apellido Materno: <input type="text" name="apellido_materno" required><br>
        Nombre del Curso: <input type="text" name="nombre_curso" required><br>
        Horas del Curso: <input type="number" name="horas" required><br>
        Fecha de Inicio: <input type="date" name="fecha_inicio" required><br>
        Fecha Final: <input type="date" name="fecha_final" required><br>
        <button type="submit">Guardar</button>
    </form>
</body>
</html>
