<?php
$file_path = 'data/cursos.json';
$pdf_preview = ''; // Variable para almacenar la ruta del PDF que se mostrará en la vista previa

if (file_exists($file_path)) {
    $cursos = json_decode(file_get_contents($file_path), true);
} else {
    $cursos = [];
}

// Manejo de búsqueda
$search_query = '';
if (isset($_POST['buscar'])) {
    $search_query = $_POST['matricula'] ?? '';
}

// Aprobación de curso
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aprobar'])) {
    $curso_id = $_POST['curso_id'] ?? '';
    foreach ($cursos as &$curso) {
        if ($curso['curso_id'] === $curso_id) {
            $curso['estado'] = 'Aprobado';

            // Crear un nuevo PDF sin marca de agua
            require_once('fpdf/fpdf.php');

            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);

            // Contenido del PDF
            $pdf->Cell(0, 10, "Curso: {$curso['nombre_curso']}", 0, 1);
            $pdf->Cell(0, 10, "Matrícula: {$curso['matricula']}", 0, 1);
            $pdf->Cell(0, 10, "Nombre: {$curso['nombres']} {$curso['apellido_paterno']} {$curso['apellido_materno']}", 0, 1);
            $pdf->Cell(0, 10, "Horas: {$curso['horas_curso']}", 0, 1);
            $pdf->Cell(0, 10, "Fecha de Inicio: {$curso['fecha_inicio']}", 0, 1);
            $pdf->Cell(0, 10, "Fecha de Fin: {$curso['fecha_fin']}", 0, 1);

            // Guardar el nuevo archivo PDF
            $approved_pdf_file = "pdfs/{$curso_id}_aprobado.pdf";
            $pdf->Output('F', $approved_pdf_file);

            // Eliminar el PDF original con marca de agua
            $watermarked_pdf_file = "pdfs/{$curso_id}.pdf";
            if (file_exists($watermarked_pdf_file)) {
                unlink($watermarked_pdf_file); // Eliminar el archivo con marca de agua
            }

            break;
        }
    }

    // Guardar los cursos en el archivo JSON
    if (file_put_contents($file_path, json_encode($cursos, JSON_PRETTY_PRINT)) === false) {
        echo "<script>alert('Error al guardar en el archivo JSON.');</script>";
    } else {
        echo "<script>alert('Curso aprobado y datos guardados correctamente.');</script>";
    }
}

// Eliminación de curso
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    $curso_id = $_POST['curso_id'] ?? '';
    foreach ($cursos as $key => $curso) {
        if ($curso['curso_id'] === $curso_id) {
            // Eliminar el archivo PDF correspondiente
            $pdf_file = "pdfs/{$curso_id}.pdf";
            $approved_pdf_file = "pdfs/{$curso_id}_aprobado.pdf";
            if (file_exists($pdf_file)) {
                unlink($pdf_file);
            }
            if (file_exists($approved_pdf_file)) {
                unlink($approved_pdf_file);
            }
            // Eliminar la entrada del curso
            unset($cursos[$key]);
            break;
        }
    }

    // Guardar los cursos en el archivo JSON
    if (file_put_contents($file_path, json_encode(array_values($cursos), JSON_PRETTY_PRINT)) === false) {
        echo "<script>alert('Error al guardar en el archivo JSON.');</script>";
    } else {
        echo "<script>alert('Curso eliminado y datos guardados correctamente.');</script>";
    }
}

// Filtrar cursos por matrícula
if ($search_query) {
    $cursos = array_filter($cursos, function ($curso) use ($search_query) {
        return stripos($curso['matricula'], $search_query) !== false; // Búsqueda sin distinguir mayúsculas
    });
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Revisar Cursos</title>
    <style>
        /* Modal PDF */
        .modal-dialog {
            max-width: 90%;
        }
        iframe {
            width: 100%;
            height: 400px;
            border: none;
        }

        /* Estilo para el botón de actualizar en la parte derecha */
        .refresh-btn {
            position: absolute;
            top: 20px;
            right: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Revisar Cursos</h1>

        <!-- Botón de actualización -->
        <button class="btn btn-secondary refresh-btn" onclick="location.reload()">Actualizar</button>

        <!-- Campo de búsqueda -->
        <form method="POST" class="form-inline mb-3">
            <input type="text" name="matricula" class="form-control" placeholder="Buscar por Matrícula" value="<?= htmlspecialchars($search_query) ?>">
            <button type="submit" name="buscar" class="btn btn-primary ml-2">Buscar</button>
        </form>

        <table class="table table-bordered mt-4">
            <thead class="thead-light">
                <tr>
                    <th>Matrícula</th>
                    <th>Nombre</th>
                    <th>Apellido Paterno</th>
                    <th>Estado</th>
                    <th>Curso</th>
                    <th>PDF</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cursos as $curso): ?>
                    <tr>
                        <td><?= htmlspecialchars($curso['matricula']) ?></td>
                        <td><?= htmlspecialchars($curso['nombres']) ?></td>
                        <td><?= htmlspecialchars($curso['apellido_paterno']) ?></td>
                        <td><?= htmlspecialchars($curso['estado']) ?></td>
                        <td><?= htmlspecialchars($curso['nombre_curso']) ?></td>
                        <td>
                            <button class="btn btn-info" data-toggle="modal" data-target="#pdfModal" onclick="previewPdf('<?= htmlspecialchars($curso['curso_id']) ?>')">
                                <?= $curso['estado'] === 'No Aprobado' ? 'Ver PDF No Aprobado' : 'Ver PDF Aprobado' ?>
                            </button>
                        </td>
                        <td>
                            <?php if ($curso['estado'] === 'No Aprobado'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="curso_id" value="<?= htmlspecialchars($curso['curso_id']) ?>">
                                    <button type="submit" name="aprobar" class="btn btn-success">Aprobar</button>
                                    <button type="submit" name="eliminar" class="btn btn-danger">Eliminar</button>
                                </form>
                            <?php else: ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="curso_id" value="<?= htmlspecialchars($curso['curso_id']) ?>">
                                    <button type="submit" name="eliminar" class="btn btn-danger">Eliminar</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal para vista previa del PDF -->
    <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pdfModalLabel">Vista Previa del Certificado</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe id="pdfPreview" src="" frameborder="0"></iframe>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function previewPdf(curso_id) {
            const estado = <?= json_encode($cursos) ?>.find(curso => curso.curso_id === curso_id).estado;
            const pdfPath = estado === 'Aprobado' ? `pdfs/${curso_id}_aprobado.pdf` : `pdfs/${curso_id}.pdf`;
            document.getElementById('pdfPreview').src = pdfPath;
        }
    </script>
</body>
</html>
