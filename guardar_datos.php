<?php
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$file_path = 'data/cursos.json';
$pdf_generated = false;
$pdf_file = ''; // Ruta del archivo PDF generado

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['excel_file'])) {
        // Lógica para manejar el archivo Excel
    } else {
        $matricula = strtoupper($_POST['matricula']);
        $nombres = strtoupper($_POST['nombres']);
        $apellido_paterno = strtoupper($_POST['apellido_paterno']);
        $apellido_materno = strtoupper($_POST['apellido_materno']);
        $correo_electronico = $_POST['correo_electronico']; // Nuevo campo
        $nombre_curso = strtoupper($_POST['nombre_curso']);
        $horas_curso = $_POST['horas_curso'];
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = $_POST['fecha_fin'];

        $curso_id = uniqid();

        // Datos del curso
        $curso = [
            'curso_id' => $curso_id,
            'matricula' => $matricula,
            'nombres' => $nombres,
            'apellido_paterno' => $apellido_paterno,
            'apellido_materno' => $apellido_materno,
            'correo_electronico' => $correo_electronico, // Almacena el correo pero no lo usaremos en el PDF
            'nombre_curso' => $nombre_curso,
            'horas_curso' => $horas_curso,
            'fecha_inicio' => $fecha_inicio,
            'fecha_fin' => $fecha_fin,
            'estado' => 'No Aprobado',
        ];

        // Guardar datos en JSON
        if (file_exists($file_path)) {
            $cursos = json_decode(file_get_contents($file_path), true);
        } else {
            $cursos = [];
        }

        $cursos[] = $curso;
        file_put_contents($file_path, json_encode($cursos, JSON_PRETTY_PRINT));

        // Crear PDF
        require_once('fpdf/fpdf.php');
        require_once('fpdf/PDF_RotatedText.php');

        $pdf = new PDF_RotatedText();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        // Contenido del PDF
        $pdf->Cell(0, 10, "CURSO: $nombre_curso", 0, 1);
        $pdf->Cell(0, 10, "MATRÍCULA: $matricula", 0, 1);
        $pdf->Cell(0, 10, "NOMBRE: $nombres $apellido_paterno $apellido_materno", 0, 1);
        $pdf->Cell(0, 10, "HORAS: $horas_curso", 0, 1);
        $pdf->Cell(0, 10, "FECHA DE INICIO: $fecha_inicio", 0, 1);
        $pdf->Cell(0, 10, "FECHA DE FIN: $fecha_fin", 0, 1);

        // Añadir marca de agua
        $pdf->RotatedText(35, 190, 'NO VÁLIDO', 45);

        // Guardar PDF
        $pdf_file = "pdfs/$curso_id.pdf";
        $pdf->Output('F', $pdf_file);

        $pdf_generated = true;

        // Enviar correo electrónico con PHPMailer
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Servidor SMTP proporcionado
        $mail->SMTPAuth = true;
        $mail->Username = 'a20490223@itmexicali.edu.mx'; // Tu correo de SMTP
        $mail->Password = 'enojlkmmucycnzmd'; // Tu contraseña SMTP
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('a20490223@itmexicali.edu.mx', 'Sistema de Certificados');
        $mail->addAddress($correo_electronico); // Correo electrónico del maestro
        $mail->addAttachment($pdf_file); // Adjunta el PDF

        $mail->isHTML(true);
        $mail->Subject = 'Tu Certificado de Curso';
        $mail->Body = "
            <h1>¡Gracias por registrarte!</h1>
            <p>A continuación encontrarás adjunto tu certificado del curso <strong>$nombre_curso</strong>.</p>
            <p>Detalles del curso:</p>
            <ul>
                <li>Matrícula: $matricula</li>
                <li>Nombre: $nombres $apellido_paterno $apellido_materno</li>
                <li>Horas del curso: $horas_curso</li>
                <li>Fecha de inicio: $fecha_inicio</li>
                <li>Fecha de fin: $fecha_fin</li>
            </ul>
        ";

        if (!$mail->send()) {
            // Si el correo no se envía, puedes manejarlo como desees
            echo 'El correo no pudo ser enviado. <br> Mailer Error: ' . $mail->ErrorInfo;
        } else {
            echo '<script>
                    alert("Certificado registrado");
                  </script>';
        }
        
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Datos</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
        }
        .container {
            display: flex;
            justify-content: space-between;
        }
        .form-section, .upload-section {
            flex: 0 0 48%;
        }
        .form-group {
            margin-bottom: 15px;
        }
        h3 {
            text-align: center; /* Centrar el título */
            margin-top: 20px; /* Espacio arriba del título */
            margin-bottom: 20px; /* Espacio abajo del título */
        }
        h1 {
            margin-bottom: 40px; /* Más espacio abajo del título principal */
            text-align: center; /* Centrar el título principal */
        }
        #popup-message {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            border: 1px solid #ccc;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
            z-index: 1000;
        }
        #popup-message .close {
            cursor: pointer;
            float: right;
        }
    </style>
</head>
<body>
    <h1>Registrar Datos</h1>
    <div class="container">
        <div class="form-section">
            <h3>Formulario de Registro</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="matricula">Matrícula:</label>
                        <input type="text" name="matricula" class="form-control" required oninput="this.value = this.value.toUpperCase();">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="nombres">Nombres:</label>
                        <input type="text" name="nombres" class="form-control" required oninput="this.value = this.value.toUpperCase();">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="apellido_paterno">Apellido Paterno:</label>
                        <input type="text" name="apellido_paterno" class="form-control" required oninput="this.value = this.value.toUpperCase();">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="apellido_materno">Apellido Materno:</label>
                        <input type="text" name="apellido_materno" class="form-control" required oninput="this.value = this.value.toUpperCase();">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nombre_curso">Nombre del Curso:</label>
                        <input type="text" name="nombre_curso" class="form-control" required oninput="this.value = this.value.toUpperCase();">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="horas_curso">Horas del Curso:</label>
                        <input type="number" name="horas_curso" class="form-control" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="fecha_inicio">Fecha de Inicio:</label>
                        <input type="date" name="fecha_inicio" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="fecha_fin">Fecha de Fin:</label>
                        <input type="date" name="fecha_fin" class="form-control" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="correo_electronico">Correo Electrónico:</label>
                        <input type="email" name="correo_electronico" class="form-control" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </form>
        </div>
        <div class="upload-section">
            <h3>Cargar Archivo Excel</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="excel_file">Subir archivo Excel:</label>
                    <input type="file" name="excel_file" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Cargar</button>
            </form>
        </div>
    </div>
    
    <!-- Pop-up message -->
    <div id="popup-message">
        <span class="close" onclick="document.getElementById('popup-message').style.display='none'">&times;</span>
        <p>¡Registro Exitoso!.</p>
    </div>
    
    <script>
        // Cerrar el popup si el usuario hace clic fuera de él
        window.onclick = function(event) {
            var modal = document.getElementById('popup-message');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
