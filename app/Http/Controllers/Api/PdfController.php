<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pdf;

class PdfController extends Controller
{
    protected $pdf;

    public function __construct(Pdf $pdf)
    {
        $this -> fpdf = $pdf;
    }

    public function index()
    {
        // $this->fpdf->SetFont('Arial', 'B', 15);
        // $this->fpdf->AddPage('L', ['100', '100']);
        // $this->fpdf->Text(10,10, 'Hola mundo');
        $this->fpdf->AliasNbPages();
        $this->fpdf->AddPage();
        $this->fpdf->SetMargins(10,10,10);
        $this->fpdf->SetAutoPageBreak(true,20);;
        $this->fpdf->SetX(15);
        $this->fpdf->SetFont('Helvetica', 'B', 15);
        $this->fpdf->Cell(10,8,'N', 1,0,'C',0);
        $this->fpdf->Cell(60,8,'Producto', 1,0,'C',0);
        $this->fpdf->Cell(30,8,'Costo', 1,0,'C',0);
        $this->fpdf->Cell(35,8,'Cantidad', 1,0,'C',0);
        $this->fpdf->Cell(50,8,'Total', 1,1,'C',0);

        $this->fpdf->SetFillColor(233,229,235);
        $this->fpdf->SetDrawColor(61,61,61);

        // $this->fpdf->Ln(0.5);
        // $this->fpdf->setXY(30,60);
        $this->fpdf->SetFont('Arial','',9);
        for ($i=1; $i <=40; $i++) {
            $this->fpdf->setX(15);
            // $this->fpdf->Cell(50,10, utf8_decode('Imprimiendo línea número ').$i,1,1);
            $this->fpdf->Cell(10,8,$i, 1,0,'C',1);
            $this->fpdf->Cell(60,8,'Leche', 1,0,'C',0);
            $this->fpdf->Cell(30,8,'$ 20', 1,0,'C',0);
            $this->fpdf->Cell(35,8,'20', 1,0,'C',0);
            $this->fpdf->Cell(50,8,'40', 1,1,'C',0);
        }
        // $this->fpdf->Output();
        // exit;

        $pdfFile =  $this->fpdf->Output("","S");
        return response([
            'data' => chunk_split(base64_encode($pdfFile))
        ]);
    }


}
