<?php
// Aprobar un curso
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['matricula'])) {
    $matricula = $_POST['matricula'];

    // Cargar cursos desde el JSON
    $cursos = json_decode(file_get_contents('data/cursos.json'), true);

    // Buscar el curso y marcarlo como aprobado
    foreach ($cursos as &$curso) {
        if ($curso['matricula'] === $matricula) {
            $curso['aprobado'] = true;
            break;
        }
    }

    // Guardar los cambios en el JSON
    file_put_contents('data/cursos.json', json_encode($cursos, JSON_PRETTY_PRINT));

    // Guardar el curso aprobado en otro archivo
    $cursos_aprobados = json_decode(file_get_contents('data/cursos_aprobados.json'), true) ?? [];
    $cursos_aprobados[] = $curso; // Agregar el curso aprobado
    file_put_contents('data/cursos_aprobados.json', json_encode($cursos_aprobados, JSON_PRETTY_PRINT));

    // Guardar el curso aprobado para que no aparezca en la lista de cursos pendientes
    $cursos = array_filter($cursos, function($curso) use ($matricula) {
        return $curso['matricula'] !== $matricula;
    });
    file_put_contents('data/cursos.json', json_encode(array_values($cursos), JSON_PRETTY_PRINT));

    header('Location: revisar_cursos.php'); // Redireccionar a la página de revisión de cursos
    exit;
}
?>
