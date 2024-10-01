<?php
$file_path = 'data/cursos.json';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricula = $_POST['matricula'] ?? '';

    if (file_exists($file_path)) {
        $cursos = json_decode(file_get_contents($file_path), true);
        $resultado = array_filter($cursos, fn($curso) => $curso['matricula'] === $matricula);
    } else {
        $resultado = [];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consultar Certificado</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            padding: 20px;
            height: 100vh; /* Altura completa de la ventana */
            display: flex;
            align-items: center; /* Centrado vertical */
            justify-content: center; /* Centrado horizontal */
        }
        .result {
            margin-top: 20px;
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 5px;
            background-color: #f9f9f9;
            width: 100%; /* Ocupar todo el ancho disponible */
            overflow-x: auto; /* Desplazamiento horizontal si es necesario */
        }
    </style>
</head>
<body>
    <div class="container text-center">
        <h1>Consultar Certificado</h1>
        <form method="POST" class="form-inline justify-content-center">
            <div class="form-group mb-2">
                <label for="matricula" class="sr-only">Ingrese Matrícula:</label>
                <input type="text" name="matricula" class="form-control" required placeholder="Matrícula">
            </div>
            <button type="submit" class="btn btn-primary mb-2">Consultar</button>
        </form>

        <?php if (isset($resultado)): ?>
            <div class="result">
                <h2>Resultados:</h2>
                <?php if (count($resultado) > 0): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Matrícula</th>
                                <th>Nombre</th>
                                <th>Curso</th>
                                <th>Estado</th>
                                <th>PDF</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resultado as $curso): ?>
                                <tr>
                                    <td><?= htmlspecialchars($curso['matricula']) ?></td>
                                    <td><?= htmlspecialchars($curso['nombres']) ?> <?= htmlspecialchars($curso['apellido_paterno']) ?> <?= htmlspecialchars($curso['apellido_materno']) ?></td>
                                    <td><?= htmlspecialchars($curso['nombre_curso']) ?></td>
                                    <td><?= htmlspecialchars($curso['estado']) ?></td>
                                    <td>
                                        <?php if ($curso['estado'] === 'Aprobado'): ?>
                                            <a href="pdfs/<?= htmlspecialchars($curso['curso_id']) ?>_aprobado.pdf" target="_blank">Ver PDF Aprobado</a>
                                        <?php else: ?>
                                            <a href="pdfs/<?= htmlspecialchars($curso['curso_id']) ?>.pdf" target="_blank">Ver PDF (No Aprobado)</a> - Contiene Marca de Agua
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No se encontraron resultados.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
