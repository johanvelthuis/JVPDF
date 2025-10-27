<?php
require_once __DIR__ . '/../vendor/autoload.php';
use JVelthuis\JVPdf\JVPDF;

$pdf = new JVPDF();
$pdf->setTitle('JV Example Borders');
$pdf->addPage();

$fontArial = $pdf->AddFont('fonts/Arial.ttf');
$pdf->SetFont($fontArial, 12);

$html = <<<'HTML'
<style>
body {
	font-family: arial;
	font-size: 11pt;
}
.section-title {
	font-size: 16pt;
	margin: 0 0 8mm;
}
.panel {
	margin: 6mm 12mm;
	padding: 6mm 12mm;
	background-color: #f1edff;
}
.panel--left-border {
	border-left: 4mm solid #6a42ba;
}
.panel--left-border strong {
	display: block;
	margin-bottom: 2mm;
}
.panel--frame {
	border: 1.2mm solid #1565c0;
	background-color: #e3f2fd;
}
.panel--frame .note {
	display: inline-block;
	margin-top: 3mm;
	margin-right: 4mm;
	padding: 2mm 4mm 3mm;
	border: 0.8mm solid #0d47a1;
	background-color: #ffffff;
}
.panel--mixed {
	border-left: 3mm solid #ff9800;
	border-top: 0.8mm solid #ffa726;
	border-bottom: 0.8mm solid #ffa726;
	background-color: #fff3e0;
	padding: 8mm 12mm 8mm 16mm;
}
.panel--mixed .badge {
	display: inline-block;
	border: 0.6mm solid #ef6c00;
	padding: 2mm 4mm;
	margin-right: 4mm;
	background-color: #ffe0b2;
}
</style>

<h1 class="section-title">Borders &amp; Padding demonstratie</h1>

<div class="panel panel--left-border">
	<strong>Eenzijdige border:</strong>
	Deze paragraaf gebruikt een dikke linkerrand in combinatie met royale padding.
	Hier controleren we dat de border exact gelijk loopt met de achtergrondkleur,
	zonder uitstekende uiteinden.
</div>

<div class="panel panel--frame">
	<strong>Volledig kader:</strong>
	De combinatie van padding en een uniforme border moet een strak kader opleveren.
	In de inline voorbeelden hieronder controleren we dat rand en achtergrond netjes op
	elkaar aansluiten, ook wanneer elementen binnen het blok inline-block randen gebruiken.
	<span class="note">Inline voorbeeld A</span>
	<span class="note">Nog een element</span>
</div>

<div class="panel panel--mixed">
	<strong>Gecombineerde borderstijlen:</strong>
	Links een brede kleurband, boven en onder een dunne lijn. Dankzij extra linkerpading
	mag de tekst niet tegen de border plakken. De badges tonen dat nested borders in de padding-zone
	ook gelijk moeten lopen.
	<div style="margin-top: 4mm;">
		<span class="badge">Badge 1</span>
		<span class="badge">Badge 2</span>
	</div>
</div>
HTML;

$pdf->pdf->writeHTML($html, true, false, true, false, '');
$pdf->render('output.pdf');
