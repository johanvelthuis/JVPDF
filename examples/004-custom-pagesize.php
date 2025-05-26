<?php
require_once __DIR__ . '/../vendor/autoload.php';
use JVelthuis\JVPdf\JVPDF;

// Create a new PDF document with custom pagesize.
$pdf = new JVPDF();
$pdf->setTitle('JV Example 1');

$pdf->addPage('100:100');

$fontImpact = $pdf->AddFont('fonts/impact.ttf');
$pdf->SetFont($fontImpact, 20);

$pdf->pdf->Rect(25,25,50,50);
$pdf->pdf->setXY(28,50);
$pdf->pdf->Cell(50, 10, "Hallo Johan V");

// Render the PDF to a file
$pdf->render('output.pdf');
