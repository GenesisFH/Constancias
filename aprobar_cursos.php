<?php
require('fpdf/fpdf.php');

// Obtener el índice del curso a aprobar
$index = $_GET['index'];

// Leer los cursos del archivo JSON
$archivo_json = 'data/cursos.json';
$cursos = json_decode(file_get_contents($archivo_json), true);

// Actualizar el estado del curso a aprobado
$cursos[$index]['aprobado'] = true;

// Regenerar el PDF sin la marca de agua
$matricula = $cursos[$index]['matricula'];
$archivo_pdf = "pdfs/curso_$matricula.pdf";

// Crear un nuevo PDF sin la marca de agua
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(40, 10, "Información del Curso");

$pdf->Ln(20);
$pdf->Cell(40, 10, "Matrícula: " . $cursos[$index]['matricula']);
$pdf->Ln();
$pdf->Cell(40, 10, "Nombres: " . $cursos[$index]['nombres'] . " " . $cursos[$index]['apellido_paterno'] . " " . $cursos[$index]['apellido_materno']);
$pdf->Ln();
$pdf->Cell(40, 10, "Curso: " . $cursos[$index]['nombre_curso']);
$pdf->Ln();
$pdf->Cell(40, 10, "Horas: " . $cursos[$index]['horas']);
$pdf->Ln();
$pdf->Cell(40, 10, "Fecha de Inicio: " . $cursos[$index]['fecha_inicio']);
$pdf->Ln();
$pdf->Cell(40, 10, "Fecha Final: " . $cursos[$index]['fecha_final']);

$pdf->Output('F', $archivo_pdf);

// Guardar cambios en el archivo JSON
file_put_contents($archivo_json, json_encode($cursos, JSON_PRETTY_PRINT));

echo "Curso aprobado y PDF actualizado!";
?>
