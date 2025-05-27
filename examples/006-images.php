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
$tc->setXY(50,10);
$tc->Cell(50, 10, "Example 6");

$pdf->Image(__DIR__."/img/rock-pianist.jpg", 40, 50, 100);
$pdf->Image(__DIR__."/img/transparent.png", 70, 100, 50);


//$tc->ImageEps(__DIR__.'/img/logo.pdf', 50, 50, 50, 50);
$tc->ImageSVG(__DIR__.'/img/logo.svg', 50, 50, 100);
$pdf->ImagePDF(__DIR__."/img/logo.pdf", 50, 200, 75);

// Render the PDF to a file
$pdf->render('output.pdf');

