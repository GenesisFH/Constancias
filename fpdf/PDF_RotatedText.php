<?php
require_once('fpdf.php');

class PDF_RotatedText extends FPDF {
    var $angle = 0;

    function RotatedText($x, $y, $txt, $angle) {
        $this->Rotate($angle, $x, $y);
        $this->Text($x, $y, $txt);
        $this->Rotate(0);
    }

    function Rotate($angle, $x = -1, $y = -1) {
        if ($x == -1) $x = $this->x;
        if ($y == -1) $y = $this->y;
        $this->angle = $angle;
        // Lógica de rotación aquí...
    }
}
?>
