<?php
require_once __DIR__ . '/../vendor/autoload.php';
use JVelthuis\JVPdf\JVPDF;

// create PDF
$pdf = new JVPDF();
$pdf->setTitle('JV Example Margins & Padding');
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
	margin: 0 0 6mm;
}
.box {
	background-color: #f2f5ff;
	border: 0.3mm solid #6a7eb3;
	margin: 8mm 14mm;
	padding: 6mm;
	line-height: 1.35;
}
.box--top-heavy {
	background-color: #fef4e6;
	border-color: #d9984a;
	margin-top: 14mm;
	padding-top: 10mm;
	padding-bottom: 7mm;
}
.box--asymmetric {
	background-color: #e9f7ef;
	border-color: #6fbf73;
	margin: 5mm 18mm 9mm;
	padding: 5mm 8mm 12mm 14mm;
}
.box--asymmetric span.tag {
	display: inline-block;
	margin-top: 4mm;
	margin-right: 4mm;
	padding: 2mm 4mm;
	background-color: #bfe3c4;
	border-radius: 2mm;
}
.note {
	margin: 0 18mm 12mm;
	padding: 4mm 6mm 8mm;
	background-color: #ede7f6;
	border-left: 2.5mm solid #8366d3;
}
.inline-stack {
	margin: 6mm 22mm 4mm;
}
.inline-stack span {
	display: inline-block;
	background-color: #ffe8cc;
	border: 0.2mm solid #d3a166;
	margin: 0 4mm 4mm 0;
	padding: 2.5mm 6mm;
}
.inline-stack span:last-child {
	padding: 3mm 8mm 5mm 10mm;
	margin-bottom: 6mm;
}
code {
	font-family: monospace;
	font-size: 10pt;
}
</style>

<h1 class="section-title">Margins &amp; Padding demonstratie</h1>

<div class="box">
	<strong>Symmetrische ruimte:</strong> Deze standaardbox heeft aan alle zijden dezelfde margin en padding.
	De tekst loopt bewust over meerdere regels zodat zichtbaar wordt dat zowel de boven- als onderruimte
	behouden blijft wanneer de alinea wordt afgebroken. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
</div>

<div class="box box--top-heavy">
	<strong>Extra top padding:</strong> Door <code>padding-top</code> te vergroten ontstaat extra ademruimte boven de inhoud.
	De aangepaste <code>margin-top</code> zorgt er bovendien voor dat deze sectie loskomt van de vorige box.
	Omdat deze tekst eveneens meerdere regels vult, wordt duidelijk dat de onderzijde niet instort.
</div>

<div class="box box--asymmetric">
	<strong>Asymmetrische padding:</strong> Links is meer ruimte gereserveerd, rechts juist iets minder.
	Dit helpt wanneer we content visueel in een kolom willen duwen.
	<span class="tag">Inline blok A</span>
	<span class="tag">Met extra padding</span>
	<span class="tag" style="padding: 3mm 5mm 6mm 9mm;">Eigen stijl</span>
</div>

<div class="note">
	Dit blok gebruikt alleen CSS‑margin en -padding; er is geen border rondom.
	De linkerkant heeft een opvallende <code>border-left</code> maar de overige ruimte wordt volledig door
	padding gerealiseerd. Ook hier forceren we een langere paragraaf zodat de onderste padding zichtbaar blijft.
</div>

<div class="inline-stack">
	<span>Inline block A</span>
	<span>Inline block met<br />meer inhoud</span>
	<span>Custom padding op laatste item</span>
</div>
HTML;

$pdf->pdf->writeHTML($html, true, false, true, false, '');
$pdf->render('output.pdf');
