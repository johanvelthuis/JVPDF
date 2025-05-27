<?php

require_once __DIR__.'/../vendor/autoload.php';


//TCPDF Example 61 gaat over XHTML + CSS met fontkeuze.
//TCPDF kan ook met html tables doen.
// create new PDF document
//$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf = new \setasign\Fpdi\Tcpdf\Fpdi();
$pageCount = $pdf->setSourceFile('template.pdf');
$tplIdx = $pdf->importPage(1);
$pdf->AddPage();
$pdf->useTemplate($tplIdx);

$pdf->setSourceFile('img/logo.pdf');
$logoIdx = $pdf->importPage(1);

// set document information
$pdf->setCreator(PDF_CREATOR);
$pdf->setAuthor('Nicola Asuni');
$pdf->setTitle('TCPDF Example 001');
$pdf->setSubject('TCPDF Tutorial');
$pdf->setKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
$pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
$pdf->setFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//// set some language-dependent strings (optional)
//if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
//	require_once(dirname(__FILE__).'/lang/eng.php');
//	$pdf->setLanguageArray($l);
//}

// ---------------------------------------------------------

// set default font subsetting mode
//$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
//$pdf->setFont('dejavusans', '', 14, '', true);

$fontPath = __DIR__.'/fonts/impact.ttf';
$fontName = TCPDF_FONTS::addTTFfont($fontPath);
$pdf->SetFont('impact', '', 24);

// Add a page
// This method has several options, check the source code documentation for more information.
//$pdf->AddPage();

// set text shadow effect
$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

// Set some content to print
$html = <<<EOD
<h1>Welcome to <a href="http://www.tcpdf.org" style="text-decoration:none;background-color:#CC0000;color:black;">&nbsp;<span style="color:black;">TC</span><span style="color:white;">PDF</span>&nbsp;</a>!</h1>
<i>This is the first example of TCPDF library.</i>
<p>This text is printed using the <i>writeHTMLCell()</i> method but you can also use: <i>Multicell(), writeHTML(), Write(), Cell() and Text()</i>.</p>
<p>Please check the source code documentation and other examples for further information.</p>
<p style="color:#CC0000;">TO IMPROVE AND EXPAND TCPDF I NEED YOUR SUPPORT, PLEASE <a href="http://sourceforge.net/donate/index.php?group_id=128076">MAKE A DONATION!</a></p>
EOD;

// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

$pdf->Image(__DIR__.'/img/rock-pianist.jpg', 50, 50, 150, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
$pdf->Image(__DIR__.'/img/transparent.png', 50, 50, 150, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

$width = $pdf->GetStringWidth('Hallo, werkt dit ook met spaties.');
$pdf->setY(200);
$pdf->Write(10, "Hallo, werkt dit ook met spaties.");

$pdf->Line(PDF_MARGIN_LEFT,$pdf->GetY() + 10, PDF_MARGIN_LEFT + $width, $pdf->GetY() + 10);

// ---------------------------------------------------------
$pdf->ImageSVG('img/logo.svg', 50, 200, 30,30);//??Zie ik nog niet. Maar misschien is de eps niet goed.

$pdf->useTemplate($logoIdx, 100, 200, 50, 50, false);
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output(__DIR__.'/example_001.pdf', 'F');

//============================================================+
// END OF FILE
//====================
