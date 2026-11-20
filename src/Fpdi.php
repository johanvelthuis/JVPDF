<?php

namespace JVelthuis\JVPdf;

class Fpdi extends \setasign\Fpdi\Tcpdf\Fpdi {

	private array $pngPdfFragments = [];
	private const LAYOUT_PIXEL = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAADElEQVR4nGNgYGAAAAAEAAH2FzhVAAAAAElFTkSuQmCC';

	/**
	 * Reuse an image-only PDF for these exact PNG bytes; HTML image layout stays with TCPDF.
	 * The caller owns cache validity and passes a fragment matching the complete PNG canvas.
	 */
	public function registerPngPdfFragment(string $imageData, string $pdfPath): void{
		$this->setSourceFile($pdfPath);
		$this->pngPdfFragments[hash('sha256', $imageData)] = $this->importPage(1);
	}

	protected function ImagePngAlpha($file, $x, $y, $wpx, $hpx, $w, $h, $type, $link, $align, $resize, $dpi, $palign, $filehash = ''){
		$template = $this->pngPdfFragments === [] ? null : ($this->pngPdfFragments[hash_file('sha256', $file)] ?? null);
		if($template === null){
			return parent::ImagePngAlpha($file, $x, $y, $wpx, $hpx, $w, $h, $type, $link, $align, $resize, $dpi, $palign, $filehash);
		}

		// Keep TCPDF's image flow, links and cursor movement, skipping only raster painting.
		parent::Image('@'.base64_decode(self::LAYOUT_PIXEL), $x, $y, $w, $h, 'PNG', $link, $align, false, $dpi, $palign, false, false, 0, false, false, false, true);
		if($palign === 'L'){
			$left = $this->lMargin;
		}elseif($palign === 'C'){
			$left = ($this->w + $this->lMargin - $this->rMargin - $w) / 2;
		}elseif($palign === 'R'){
			$left = $this->w - $this->rMargin - $w;
		}else{
			$left = $this->rtl ? $this->img_rb_x - 2 * $w : $this->img_rb_x - $w;
		}
		$this->useTemplate($template, $left, $this->img_rb_y - $h, $w, $h);
	}

	public $templateCallback;
	
	/**
	 * @var \JVelthuis\JVPdf\JVPDF;
	 */
	public $wrapper;
	
	
	public function AddPage($orientation = '', $format = '', $keepmargins = false, $tocpage = false){
		parent::AddPage($orientation, $format, $keepmargins, $tocpage);
		if(isset($this->wrapper) && $this->wrapper->useTemplateWithAddNewPage ){
			$this->wrapper->pdf->useTemplate($this->wrapper->template);
		}
	}
	
	public function AddBlankPage($orientation = '', $format = '', $keepmargins = false, $tocpage = false){
		return parent::AddPage($orientation, $format, $keepmargins, $tocpage);
	}
	
	
}
