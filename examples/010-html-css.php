<?php
require_once __DIR__ . '/../vendor/autoload.php';
use JVelthuis\JVPdf\JVPDF;

// Create a new PDF document
$pdf = new JVPDF();
$pdf->setTitle('JV Example 1');

$pdf->addPage();

$fontArial = $pdf->AddFont('fonts/Arial.ttf');
error_log("addFont: $fontArial");
$fontArialBold = $pdf->AddFont('fonts/arial-bold.ttf');//Dit is voldoende om ervoor te zorgen,
error_log("addFont: $fontArialBold");
$fontImpact = $pdf->AddFont('fonts/impact.ttf');
error_log("addFont: $fontImpact");
//dat bold tekst ook als bold wordt weergegeven.
$pdf->SetFont($fontArial, 20, '');
//$pdf->SetFont($fontArialBold, 20, 'B');
//$pdf->SetFont($fontImpact, 12);

// set font
//$pdf->pdf->SetFont('dejavusans', '', 12);
$pdf->SetXY(50,50);
//LET OP style attr moet met dubbele qoutes, enkele worden door tcpdf niet herkend.
$style = <<<EOF
<style>
h1 {
 font-family: impact;
}
</style>
EOF;
$pdf->pdf->writeHTML("{$style}<h1>Hello World!</h1><h2>Ondertitel</h2><div>Dit is mijn <b style=\"color:red;\">alinea</b>. Wat kunnen wij verder van dit bestand verwachten. Ik hoop dat er een paar regels komen bijvoorbeeld.</div>");


// test some inline CSS
$html = '<p>This is just an example of html code to demonstrate some supported CSS inline styles.
<span style="font-weight: bold;">bold text</span>
<span style="text-decoration: line-through;">line-trough</span>
<span style="text-decoration: underline line-through;">underline and line-trough</span>
<span style="color: rgb(0, 128, 64);">color</span>
<span style="background-color: rgb(255, 0, 0); color: rgb(255, 255, 255);">background color</span>
<span style="font-weight: bold;">bold</span>
<span style="font-size: xx-small;">xx-small</span>
<span style="font-size: x-small;">x-small</span>
<span style="font-size: small;">small</span>
<span style="font-size: medium;">medium</span>
<span style="font-size: large;">large</span>
<span style="font-size: x-large;">x-large</span>
<span style="font-size: xx-large;">xx-large</span>
</p>';

$pdf->pdf->WriteHTML($html);

//Documentation:
//https://tcpdf.org/examples/example_061/

$url = __DIR__."/img/transparent.png";

// create some HTML content
$html = <<<"EOF"
<h1>Image alignments on HTML table</h1>
<table cellpadding="1" cellspacing="1" border="1" style="text-align:center;">
<tr><td><img src="$url" border="0" height="41" width="41" /></td></tr>
<tr style="text-align:left;"><td><img src="$url" border="0" height="41" width="41" align="top" /></td></tr>
<tr style="text-align:center;"><td><img src="$url" border="0" height="41" width="41" align="middle" /></td></tr>
<tr style="text-align:right;"><td><img src="$url" border="0" height="41" width="41" align="bottom" /></td></tr>
<tr><td style="text-align:left;"><img src="$url" border="0" height="41" width="41" align="top" /></td></tr>
<tr><td style="text-align:center;"><img src="$url" border="0" height="41" width="41" align="middle" /></td></tr>
<tr><td style="text-align:right;"><img src="$url" border="0" height="41" width="41" align="bottom" /></td></tr>
</table>
EOF;

// output the HTML content
$pdf->pdf->writeHTML($html, true, false, true, false, '');


// Render the PDF to a file
$pdf->render('output.pdf');
