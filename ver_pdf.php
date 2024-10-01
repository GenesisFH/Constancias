<?php
if (isset($_GET['archivo'])) {
    $archivo = $_GET['archivo'];
    $ruta_pdf = 'pdfs/' . $archivo;

    if (file_exists($ruta_pdf)) {
        // Forzar la descarga del PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($ruta_pdf) . '"');
        readfile($ruta_pdf);

        // Eliminar el archivo PDF después de mostrarlo
        unlink($ruta_pdf);
    } else {
        echo "El archivo PDF no existe.";
    }
} else {
    echo "No se ha especificado ningún archivo.";
}
?>
