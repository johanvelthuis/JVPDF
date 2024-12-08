<?php
require_once __DIR__ . '/../vendor/autoload.php';

use JVelthuis\JVPdf\JVPDF;

try{
	$pdf = JVPDF::load("template.pdf");
	
	$fontImpact = $pdf->AddFont('fonts/Arial.ttf');
	$pdf->SetFont($fontImpact, 24);
	
	$pdf->pdf->Cell(50, 50, "Example 2");
	//// Add a new page, currentpage is automatically set to this new page.
	$pdf->addPageFromTemplate();
	
	$pdf->pdf->Cell( 50, 50, "Hallo Page 2");//text is added to currentpage.
	
	// Render the PDF to a file
	$pdf->render('output.pdf');
	
}catch(Exception $e){
	echo "Foutmelding: {$e->getMessage()}";
}

