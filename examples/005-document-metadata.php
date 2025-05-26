<?php

require_once __DIR__ . '/../vendor/autoload.php';

use JVelthuis\JVPdf\JVPDF;
use LaminasPdf\Font as LaminasFont;

// Create a new PDF document
$pdf = new JVPDF();

$tc = $pdf->pdf;
$tc->setTitle('Mijn Eerste PDF');
$tc->setAuthor('Johan Velthuis');
$tc->setKeywords('PHP, TCPDF FPDI, PDF, Metadata');
$tc->setSubject('Voorbeeld PDF Metadata');


//of
$pdf->setTitle("Mijn Eerste PDF Edit");


$pdf->addPage();

$fontImpact = $pdf->AddFont('fonts/impact.ttf');
$pdf->SetFont($fontImpact, 24);

$pdf->pdf->Cell(50, 50, "Example 5");

// Render the PDF to a file
$pdf->render('output.pdf');



