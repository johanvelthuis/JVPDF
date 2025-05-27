<?php
require_once __DIR__ . '/../vendor/autoload.php';
use JVelthuis\JVPdf\JVPDF;

// Create a new PDF document
$pdf = new JVPDF();
$pdf->setTitle('JV Example 1');

$pdf->addPage();

$fontImpact = $pdf->AddFont('fonts/impact.ttf');
$pdf->SetFont($fontImpact, 24);

$pdf->pdf->Cell(50, 50, "Hallo Johan V");

$pdf->merge(__DIR__ . '/attachment-2pages.pdf');


// Render the PDF to a file
$pdf->render('output.pdf');
