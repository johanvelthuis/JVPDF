<?php

namespace JVelthuis\JVPdf\Tests;

use JVelthuis\JVPdf\JVPDF;
use PHPUnit\Framework\TestCase;

class PngPdfFragmentTest extends TestCase {

	private string $directory;
	private string $imageData;
	private string $otherImageData;

	protected function setUp(): void{
		$this->directory = sys_get_temp_dir().'/jvpdf-fragment-'.bin2hex(random_bytes(8));
		mkdir($this->directory);
		$image = imagecreatetruecolor(120, 60);
		imagealphablending($image, false);
		imagesavealpha($image, true);
		imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
		imagefilledellipse($image, 50, 30, 85, 35, imagecolorallocatealpha($image, 20, 40, 90, 35));
		imageline($image, 8, 50, 110, 8, imagecolorallocatealpha($image, 20, 40, 90, 0));
		ob_start();
		imagepng($image);
		$this->imageData = ob_get_clean();
		imagefilledrectangle($image, 45, 20, 75, 40, imagecolorallocatealpha($image, 170, 30, 10, 0));
		ob_start();
		imagepng($image);
		$this->otherImageData = ob_get_clean();

		$fragment = new JVPDF();
		$fragment->pdf->SetAutoPageBreak(false, 0);
		$fragment->pdf->SetMargins(0, 0, 0);
		$fragment->pdf->AddPage('L', [40, 20]);
		$fragment->pdf->Image('@'.$this->imageData, 0, 0, 40, 20, 'PNG');
		$fragment->pdf->Output($this->directory.'/fragment.pdf', 'F');
	}

	protected function tearDown(): void{
		foreach(glob($this->directory.'/*') as $file){
			unlink($file);
		}
		rmdir($this->directory);
	}

	private function createDocument(bool $useFragment): array{
		$wrapper = new JVPDF();
		$pdf = $wrapper->pdf;
		$pdf->SetMargins(15, 15, 15);
		$pdf->SetAutoPageBreak(true, 15);
		$pdf->SetFont('helvetica', '', 11);
		if($useFragment){
			$wrapper->registerPngPdfFragment($this->imageData, $this->directory.'/fragment.pdf');
		}
		$image = '<img src="@'.base64_encode($this->imageData).'" width="40mm" />';
		$otherImage = '<img src="@'.base64_encode($this->otherImageData).'" width="40mm" />';
		$html = '<div nobr="true">Before the signature.<br /><br /><b>Kind regards,</b><br />'.$image.'<br />Name<br /><i>Title</i></div>';
		$positions = [];
		foreach([20, 240, 255, 270] as $y){
			$pdf->AddPage();
			$pdf->SetFillColor(215, 230, 245);
			$pdf->Rect(15, 15, 180, 267, 'F');
			$pdf->SetXY(15, $y);
			$wrapper->WriteHtml($html, false);
			$wrapper->WriteHtml('<br />Text after the complete signature.');
			$positions[] = [$pdf->getPage(), $pdf->GetX(), $pdf->GetY()];
		}
		$pdf->AddPage();
		$wrapper->WriteHtml('Inline <b>before</b> '.$image.' <i>after</i> and repeated '.$image.' then text.');
		$wrapper->WriteHtml('<table border="1"><tr><td>Left</td><td>'.$image.'Right</td></tr></table>');
		$wrapper->WriteHtml('An unregistered image remains unchanged: '.$otherImage);
		$pdf->AddPage();
		$pdf->setRTL(true);
		$wrapper->WriteHtml('Right '.$image.' after');
		$pdf->setRTL(false);
		$pdf->AddPage();
		foreach(['L', 'C', 'R'] as $align){
			$pdf->Image('@'.$this->imageData, 50, $pdf->GetY(), 40, 0, 'PNG', 'https://example.com', 'N', false, 300, $align);
			$pdf->Ln(5);
			$positions[] = [$pdf->getPage(), $pdf->GetX(), $pdf->GetY()];
		}
		return [$pdf, $positions];
	}

	public function testFragmentPreservesHtmlFlowAndAppearance(): void{
		[$png, $pngPositions] = $this->createDocument(false);
		[$fragment, $fragmentPositions] = $this->createDocument(true);
		$this->assertSame($pngPositions, $fragmentPositions);
		$this->assertSame($png->getNumPages(), $fragment->getNumPages());
		$this->assertGreaterThan(7, $png->getNumPages(), 'Exercise actual nobr page breaks.');
		$png->Output($this->directory.'/png.pdf', 'F');
		$fragment->Output($this->directory.'/cached.pdf', 'F');

		exec('command -v pdftoppm', $paths, $status);
		if($status !== 0){
			$this->markTestIncomplete('Geometry passed; install Poppler for the pixel comparison.');
		}
		foreach(['png', 'cached'] as $mode){
			$command = escapeshellarg($paths[0]).' -r 150 -png '.escapeshellarg($this->directory.'/'.$mode.'.pdf').' '.escapeshellarg($this->directory.'/'.$mode);
			exec($command, $output, $status);
			$this->assertSame(0, $status);
		}
		$pages = glob($this->directory.'/png-*.png');
		$this->assertNotEmpty($pages);
		foreach($pages as $page){
			$cachedPage = str_replace('/png-', '/cached-', $page);
			$this->assertSame(hash_file('sha256', $page), hash_file('sha256', $cachedPage), basename($page));
		}
	}

	public function testFailedRegistrationLeavesOriginalPngUsable(): void{
		file_put_contents($this->directory.'/invalid.pdf', 'not a PDF');
		$pdf = new JVPDF();
		try{
			$pdf->registerPngPdfFragment($this->imageData, $this->directory.'/invalid.pdf');
			$this->fail('An invalid fragment must be reported to the caller.');
		}catch(\setasign\Fpdi\PdfParser\PdfParserException $exception){
			$this->assertNotEmpty($exception->getMessage());
		}
		$pdf->pdf->AddPage();
		$pdf->pdf->Image('@'.$this->imageData, 15, 15, 40, 20, 'PNG');
		$this->assertStringStartsWith('%PDF-', $pdf->pdf->Output('', 'S'));
	}
}
