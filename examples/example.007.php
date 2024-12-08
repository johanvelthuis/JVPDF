<?php
require_once __DIR__ . '/../vendor/autoload.php';
use JVelthuis\JVPdf\JVPDF;

// Create a new PDF document
$pdf = new JVPDF();
$pdf->setTitle('JV Example 1');

$pdf->addPage();

$fontImpact = $pdf->AddFont('fonts/impact.ttf');
$pdf->SetFont($fontImpact, 24);

$tc = $pdf->pdf;
$x = 50;
$y = 50;
$pdf->SetXY($x,$y);
$str = 'Hallo Johan V';
$tc->Cell(50, 10, $str);

$width = $tc->GetStringWidth($str);//met het huidige font, maar je kunt ook een ander font opgeven.

$y += 10;
$pdf->Line($x, $y, $x + $width, $y );


// Render the PDF to a file
$pdf->render('output.pdf');
