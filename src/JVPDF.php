<?php

namespace JVelthuis\JVPdf;

use \setasign\Fpdi\Tcpdf\Fpdi;
use TCPDF_FONTS;

enum JVPDF_PageSize {

}

class JVPDF {
	
	protected static $fontCache = [];
	
	/**
	 * @var \JVelthuis\JVPdf\Unit
	 */
	protected Unit $units = Unit::MM;
	
	
	public Fpdi $pdf;
	protected string $title;
	
	/**
	 * @var JVPDF_Page[]
	 */
	private $pages = [];
	
	/**
	 * @var int
	 */
	private $currentPageIndex = 0;
	
	/**
	 * @var string Unique Identifier voor template in TCPDF
	 */
	private $template;
	
	
	public function __construct(){
		$this->pdf = new Fpdi();
		$this->pdf->SetPrintHeader(false);
		$this->pdf->setPrintFooter(false);
		$this->pdf->setCreator('JVPDF');
	}
	
	
	public function setTitle(string $title){
		$this->title = $title;
		$this->pdf->setTitle($title);
	}
	
	
	public static function load(string $path):JVPDF{
		// Controleer of het pad een absoluut pad is
		$path = Utility::absolutePath($path);
		
		if(!file_exists($path)){
			throw new \Exception('File not found: '.$path);
		}
		
		$pdf = new self();
		$templateId = static::_load($path, $pdf->pdf);
		$pdf->setTemplate($templateId);//als default voor newpage.
		
		return $pdf;
	}
	
	
	/**
	 * @param string                    $path
	 * @param \setasign\Fpdi\Tcpdf\Fpdi $pdf
	 * @param                           $startIndex
	 * @param                           $endIndex
	 *
	 * @return ?string;
	 * @throws \setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException
	 * @throws \setasign\Fpdi\PdfParser\Filter\FilterException
	 * @throws \setasign\Fpdi\PdfParser\PdfParserException
	 * @throws \setasign\Fpdi\PdfParser\Type\PdfTypeException
	 * @throws \setasign\Fpdi\PdfReader\PdfReaderException
	 */
	private static function _load(string $path, Fpdi $pdf, $startIndex = 1, $endIndex = 100000):?string{
		$pageCount = $pdf->setSourceFile($path);
		$firstTemplateId = null;
		
		for($i = 1;$i <= $pageCount;$i++){
			if($startIndex <= $i && $endIndex >= $i){
				$tplIdx = $pdf->importPage($i);
				if(!isset($firstTemplateId)){
					$firstTemplateId = $tplIdx;
				}
				$pdf->AddPage();
				$pdf->useTemplate($tplIdx);
			}
		}
		
		return $firstTemplateId;
	}
	
	
	
	
	public function merge($path, $startIndex = null, $endIndex = null):int{
		$pagesBefore = $this->pdf->getNumPages();
		static::_load($path, $this->pdf, $startIndex, $endIndex);
		$pagesAfter = $this->pdf->getNumPages();
		
		return $pagesAfter - $pagesBefore;
	}
	
	
	/**
	 * Voeg een nieuwe pagina toe aan het PDF-document.
	 *
	 * @param string|PageSize $size bijv PageSize::A4_Portrait of [breedte:hoogte] in mm: '210:297';
	 *
	 * @return int current page number
	 * @throws \Exception
	 */
	public function addPage(string|PageSize $size = PageSize::A4_Portrait):int{
		$pageSize = PageSize::parseDimensions($size);
		$orientation = PageSize::orientation($size);
		
		$this->pdf->AddPage($orientation, $pageSize);
		
		return $this->pdf->PageNo();
	}
	
	
	public function addPageFromTemplate(){
		$this->pdf->AddPage();
		if($this->template){
			$this->pdf->useTemplate($this->template);
			
		}else{
			throw new \Exception('Template not found');
		}
		
		return $this->pdf->PageNo();
	}

//	public function convertCoordinates($x, $y):array {
//		$page = $this->currentPage()->getLaminasPage();
//		return UnitConverter::convertCoordinates($x, $y, [$page->getWidth(), $page->getHeight() ], $this->units, $this->useTopLeftOrigin);
//	}
//
//	public function convertRect($x, $y, $w, $h):array {
//		$page = $this->currentPage()->getLaminasPage();
//		return UnitConverter::convertRect($x, $y, $w, $h, [$page->getWidth(), $page->getHeight() ], $this->units, $this->useTopLeftOrigin);
//	}
//
//	public function convertUnits(float|array $floatOrArray): array|float
//	{
//		// If the input is a single float, convert it and return
//		if (is_float($floatOrArray)) {
//			return UnitConverter::convertUnit($floatOrArray, $this->units);
//		}
//
//		// If the input is an array, map the conversion function over all elements
//		if (is_array($floatOrArray)) {
//			return array_map(fn($value) => UnitConverter::convertUnit($value, $this->units), $floatOrArray);
//		}
//
//		// If input is neither float nor array, throw an error (optional)
//		throw new \InvalidArgumentException('Input must be a float or an array of floats.');
//	}

//	public function drawText($text, $x, $y):LaminasPage {
//		$page = $this->currentPage()->getLaminasPage();
//
//		[$x, $y] = $this->convertCoordinates($x, $y);
//
//		$page->drawText($text, $x, $y);
//		return $page;
//	}
//
//	public function drawLine($x1, $y1, $x2, $y2):LaminasPage {
//		$page = $this->currentPage()->getLaminasPage();
//		[$x1, $y1] = $this->convertCoordinates($x1, $y1);
//		[$x2, $y2] = $this->convertCoordinates($x2, $y2);
//		$page->drawLine($x1, $y1, $x2, $y2);
//
//		return $page;
//	}
//
//
//	public function drawImage(string $imagePath, float $x, float $y, float $width = null, float $height = null): static{
//		// Controleer of het bestand bestaat
//		if (!file_exists($imagePath)) {
//			throw new \InvalidArgumentException("Image file not found: $imagePath");
//		}
//
//		// Bepaal het afbeeldingsformaat en laad het bestand
//		$image = match (mime_content_type($imagePath)) {
//			'image/jpeg' => new \LaminasPdf\Resource\Image\Jpeg($imagePath),
//			'image/png'  => new \LaminasPdf\Resource\Image\Png($imagePath),
//			default => throw new \InvalidArgumentException("Unsupported image format: $imagePath"),
//		};
//
//		// Als breedte en hoogte niet zijn opgegeven, gebruik de originele afmetingen
//		$originalWidth = $image->getPixelWidth();
//		$originalHeight = $image->getPixelHeight();
//
//		if ($width === null && $height === null) {
//			$width = $originalWidth;
//			$height = $originalHeight;
//		} elseif ($width !== null && $height === null) {
//			// Als alleen breedte is opgegeven, schaal de hoogte proportioneel
//			$height = $width * ($originalHeight / $originalWidth);
//		} elseif ($width === null && $height !== null) {
//			// Als alleen hoogte is opgegeven, schaal de breedte proportioneel
//			$width = $height * ($originalWidth / $originalHeight);
//		}
//
//		[$x, $y, $width, $height] = $this->convertRect($x, $y, $width, $height);
//
//		// Teken de afbeelding op de pagina
//		$this->currentPage()->getLaminasPage()->drawImage($image, $x, $y, $x + $width, $y + $height);
//
//		return $this;
//	}


//	public function drawPdf(string $pdfPath, float $x, float $y, float $w, float $h): static
//	{
//		// Stap 1: Exporteer de huidige LaminasPdf-pagina naar een tijdelijk bestand
//		$tempOriginal = tempnam(sys_get_temp_dir(), 'original_') . '.pdf';
//
//		$this->render($tempOriginal);
//
//		// Stap 2: Open het tijdelijke bestand met FPDI
//		$fpdi = new Fpdi();
//		$fpdi->setSourceFile($tempOriginal);
//		$fpdi->AddPage();
//		$templateId = $fpdi->importPage($this->currentPageIndex + 1);
//
//		// Stap 3: Voeg de gewenste PDF-inhoud toe met FPDI
//		$fpdi->useTemplate($templateId);
//		$fpdi->setSourceFile($pdfPath);
//		$importedPageId = $fpdi->importPage(1);
//		//TODO: klopt het coordinatenstelsel met onze wensen? Waarschijnlijk niet.
//		$fpdi->useTemplate($importedPageId, $x, $y, $w, $h);
//
//		// Sla de gewijzigde pagina op naar een nieuw tijdelijk bestand
//		$tempModified = tempnam(sys_get_temp_dir(), 'modified_') . '.pdf';
//
//
//		$fpdi->Output($tempModified, 'F');
//
//		// Stap 4: Open het gewijzigde bestand met LaminasPdf
//		$modifiedPdf = PdfDocument::load($tempModified);
//
//		// Stap 5: Vervang de huidige pagina in LaminasPdf
//		$this->pdf->pages[$this->currentPageIndex] = clone $modifiedPdf->pages[0];
//
//		// Verwijder tijdelijke bestanden
//		unlink($tempOriginal);
//		unlink($tempModified);
//
//		return $this;
//	}
//
	
	/**
	 * De templateId unieke string waarmee we een nieuwe pagina kunnen maken op basis van de template.
	 *
	 * @param string $templateId
	 *
	 * @return void
	 */
	public function setTemplate(string $templateId){
		$this->template = $templateId;
	}
	
	
	public function currentPage(){
		return $this->pdf->PageNo();
	}
	
	
	public function setCurrentPage($pageNo){
		$this->pdf->setPage($pageNo);
	}
	
	
	public function render(string $filename){
		$filename = Utility::absolutePath($filename);
		
		// Genereer het PDF-bestand op het bepaalde pad
		$this->pdf->Output($filename, 'F');
	}
	
	
	public function renderInline($filename = 'output.pdf'){
		$this->pdf->Output($filename, 'I');
	}
	
	
	// Hulpmethode om te controleren of een pad absoluut is
	protected static function isAbsolutePath(string $path):bool{
		return ($path[0] === '/' || $path[0] === '\\' || preg_match('/^[a-zA-Z]:[\/\\\\]/', $path) === 1);
	}
	
	
	// Methode om het lettertype in te stellen
	public function SetFont(string $fontPathOrName, int $size, string $style = ''){
		// Controleer of het pad naar een bestaand bestand verwijst
		if(file_exists($fontPathOrName)){
			// Het is een pad naar een TTF-bestand; voeg het toe en verkrijg de interne naam
			$fontName = $this->AddFont($fontPathOrName);
		}else{
			// Het is de naam van een reeds toegevoegd lettertype
			$fontName = $fontPathOrName;
		}
		
		// Stel het lettertype in met de verkregen naam, stijl en grootte
		$this->pdf->SetFont($fontName, $style, $size);
	}
	
	
	// Methode om een TTF-lettertype toe te voegen met caching
	public function AddFont(string $fontPath):string{
		$fontPath = Utility::absolutePath($fontPath);
		
		// Genereer een unieke sleutel op basis van het lettertypepad
		$fontKey = md5($fontPath);
		
		// Controleer of het lettertype al in de cache zit
		if(!isset(self::$fontCache[$fontKey])){
			// Voeg het TTF-lettertype toe en verkrijg de interne naam
			$fontName = TCPDF_FONTS::addTTFfont($fontPath);
			if($fontName === false){
				throw new \Exception("Kon het lettertype niet toevoegen: $fontPath");
			}
			// Sla de interne naam op in de cache
			self::$fontCache[$fontKey] = $fontName;
		}
		
		// Retourneer de interne naam van het lettertype
		return $fontName;
	}
	
	
	/**
	 *
	 * @param string $path Pad naar een JPG of PNG bestand (ook GIF?)
	 * @param int    $x
	 * @param int    $y
	 * @param int    $w
	 * @param        $h
	 * @param        $type
	 * @param        $link
	 *
	 * @return JVPDF
	 */
	public function Image(string $path, int $x, int $y, int $w = 0, $h = 0, $type = '', $link = ''):JVPDF{
		$path = Utility::absolutePath($path);
		$this->pdf->Image($path, $x, $y, $w,$h, $type, $link);
		return $this;
	}
	
	public function ImagePDF($path, $x = 0, $y = 0, $w = null, $h = null, $pageNumber = 1):JVPDF{
		$path = Utility::absolutePath($path);
		$this->pdf->setSourceFile($path);
		$tplIdx = $this->pdf->importPage($pageNumber);
		$this->pdf->useTemplate($tplIdx, $x, $y, $w, $h);
		return $this;
	}
	
	public function SetXY(int $x, int $y):JVPDF {
		$this->pdf->SetXY($x, $y);
		return $this;
	}
	
	public function Line(float $x, float $y, float $w = 0, float $h = 0, $style = []):JVPDF{
		$this->pdf->Line($x, $y, $w, $h, $style);
		return $this;
	}
}
