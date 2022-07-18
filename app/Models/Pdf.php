<?php

namespace App\Models;

use Codedge\Fpdf\Fpdf\Fpdf;

class Pdf extends Fpdf
{
    public function Header()
    {
        $this->SetFont('Times', 'B', 20);
        $this->Image(public_path('storage/images/logo.png'), 0, 0, 30);
        $this->setXY(60, 15);
        $this->Cell(100, 8, 'Nombre del Reporte', 0, 1, 'C', 0);
        $this->Image(public_path('storage/images/shinheky.png'), 178, 0, 35, 0); //imagen(archivo, png/jpg || x,y,tamaño)
        $this->Ln(40);
    }

    public function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'B', 10);
        // Número de página
        $this->Cell(170, 10, 'Todos los derechos reservados', 0, 0, 'C', 0);
        $this->Cell(25, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}
