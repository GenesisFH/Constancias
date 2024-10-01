<?php
if (isset($_POST['matricula'])) {
    $matricula = $_POST['matricula'];

    $cursos = json_decode(file_get_contents('data/cursos.json'), true) ?? [];

    foreach ($cursos as $key => &$curso) {
        if ($curso['matricula'] === $matricula) {
            $curso['aprobado'] = true; // Marcar como aprobado
            // Podrías realizar otras acciones aquí como eliminar el PDF original, etc.
            break;
        }
    }

    file_put_contents('data/cursos.json', json_encode($cursos, JSON_PRETTY_PRINT));
    
    header("Location: revisar_cursos.php"); // Redirigir a la página de revisar cursos
    exit;
}
?>
