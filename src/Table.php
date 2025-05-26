<?php

namespace JVelthuis\JVPdf;

use LaminasPdf\Color\Html;
use LaminasPdf\Font;
use LaminasPdf\Page as LaminasPage;
use LaminasPdf\Font as LaminasFont;
use LaminasPdf\Image;

//---- ----//
//jvFramework2
//
//---- ----//
/**
 * eerste versie op 22-sep.-2018 door Johan
 *
 * Voorbeeld:
 <code>
$table = new PdfTable();
$table->y = 70;//mm
$table->colWidth = array(25, 60, 3, 25, 60);//mm
$table->defaultFontSize = 8;//points
//eerste aanroep moet onder PdfTable staan, omdat daarin het bestand staat.
PdfTableCellStyle::$defaultsize = 8;//points

$row = $table->addRow();
$row->addCell("Leerling")->setBold()->setFontSize(10);
$row->addCell("");
$row->addCell("");
$row->addCell("Stagebieder")->setBold()->setFontSize(10);


$table->defaultRowHeight = 4;//mm
$table->render($page, $this->pdf);
 </code>
 */

/**
 * Class PdfTable
 * Gebruik deze class om een eenvoudige tabel/kolommen/cellen structuur met tekst te maken voor
 * LaminasPdf pages.
 */



class PdfTable {
	
	/**
	 * @var float in mm
	 */
	var $x = 25;//mm
	
	
	/**
	 * @var float in mm
	 */
	var $y = 25;//mm
	
	
	var $marginTop = 25;//mm
	
	
	/**
	 * @var LaminasPage
	 */
	var $page;
	
	/**
	 * @var \LaminasPdf\PdfDocument
	 */
	var $pdf;
	
	/**
	 * @var float in mm
	 */
	var $defaultColumnWidth = 25;//mm
	
	
	/**
	 * @var float in mm
	 */
	var $defaultRowHeight = 5;//mm
	
	var $defaultFont;
	var $defaultFontSize = 12;//points
	
	
	var $rows = array();
	
	/**
	 * LET OP! Het heeft pas zin om deze te gebruiken, als alle cellen zijn toegevoegd en alle rowHeights zijn ingesteld.
	 * @return float mm
	 */
	public function getHeight(){
		$yOffset = 0;
		/* @var $row PdfTableRow */
		foreach($this->rows as $row){
			$yBiggestOffset = 0;
			/* @var $cell PdfTableCell */
			foreach($row->cells as $cell){
				$explode = explode("\n", trim($cell->value));
				$cellYOffset = 0;
				foreach($explode as $value){
					if(isset($row->rowHeight)){
						$cellYOffset += $row->rowHeight;
					}else{
						$cellYOffset += $this->defaultRowHeight;
					}
				}
				if($cellYOffset > $yBiggestOffset){
					$yBiggestOffset = $cellYOffset;
				}
			}
			$yOffset += $yBiggestOffset;
		}
		return $yOffset;
	}
	
	
//	/**
//	 * @return float mm
//	 */
//	public function getWidth(){
//		/* @var $row PdfTableRow */
//		foreach($this->rows as $row){
//			$j = 0;
//			/* @var $cell PdfTableCell */
//			foreach($row->cells as $cell){
//				if(!isset($this->colWidth[$j])){
//					$this->colWidth[$j] = $this->defaultColumnWidth;
//				}
//			}
//		}
//		$width = 0;
//		foreach($this->colWidth as $colWidth){
//			$width += $colWidth;
//		}
//		return $width;
//	}
	
	
	/**
	 * PdfTable constructor.
	 *
	 * @throws \Exception
	 */
	public function __construct(){
		$this->defaultFont = Font::fontWithName(Font::FONT_HELVETICA);
	}
	
	var $colWidth = array();
	public function setColumnWidth($colNr, $width){
		$this->colWidth[$colNr] = $width;
	}
	
	
	/**
	 * @param null|PdfTableRow $row
	 *
	 * @return PdfTableRow
	 */
	public function addRow($row = null){
		if(!isset($row)){
			$row = new PdfTableRow();
			$row->defaultFont = $this->defaultFont;
			$row->defaultFontSize = $this->defaultFontSize;
		}else{
			if(is_object($row) && get_class($row) === PdfTableRow::class){
			}else{
				$items = $row;
				$row = new PdfTableRow();
				foreach($items as $item){
					$row->addCell($item);
				}
			}
		}
		$this->rows[] = $row;
		return $row;
	}
	
	
	/**
	 * @param string[]|string ...$values
	 *
	 * @return PdfTableRow
	 */
	public function addRowWithCellValue(...$values){
		$row = $this->addRow();
		foreach($values as $value){
			$row->addCell($value);
		}
		return $row;
	}
	
	public function addEmptyRow($count = 1){
		for($i=0;$i<$count;$i++){
			$row = $this->addRow();
			$row->addCell('');
		}
	}
	
	private function getHalfSpaceWidth(){
		//TODO: misschien later berekenen als de helft van een spatie van het betreffende font/grootte.
		return 20;
	}
	
	/**
	 * LET OP! $page is by reference, dus niet $this->page[0] daar rechtsstreeks inzetten!!!!!
	 *
	 * @param  $page LaminasPage
	 * @param  $pdf \LaminasPdf\PdfDocument
	 *
	 * @throws \Exception
	 */
	public function render(&$page, &$pdf){
		//log("render", 'red');
		$this->page = $page;
		$this->pdf = $pdf;
		
		$i = 0;
		$currentPageNumber = 0;
		foreach($this->pdf->pages as $currentPage){
			if($currentPage === $this->page){
				$currentPageNumber = $i;
				break;
			}
			$i++;
		}
		
		$this->page->setFont($this->defaultFont, $this->defaultFontSize);
		$style = $this->page->getStyle();
		if(isset($style)){
			$previousLineColor = $style->getLineColor();
			$previousFillColor = $style->getFillColor();
			
		}else{
			$previousLineColor = new Html('#000000');
			$previousFillColor = new Html('#000000');
		}
		
		$lastPageNumber = 0;
		
		$yBiggestOffset = array();
		
		$rowYOffset = $this->y;
		$rowNumber = 0;
		/* @var $row PdfTableRow */
		foreach($this->rows as $row){
			//ErrorLogColor::log("------------------", 'yellow');
			$rowStartPageNumber = $currentPageNumber;
			//error_log("rowStartPageNumber = $rowStartPageNumber");
			
			if(!isset($yBiggestOffset[$rowStartPageNumber])){
				$yBiggestOffset[$rowStartPageNumber] = $this->marginTop;//feitelijk rowHeight
			}
			
			
			$xOffset = $this->x;
			
			$i = 0;
			$cellYOffset = $rowYOffset;
			$pref = Voorkeuren::sharedInstance();
			$cellHighestPage = $rowStartPageNumber;
			
			if(!isset($row->rowHeight)){
				$row->rowHeight = $this->defaultRowHeight;
			}
			
			/* @var $cell PdfTableCell */
			foreach($row->cells as $cell){
				//error_log("cell");
				
				$cellYOffset = $rowYOffset;
				$currentPageNumber = $rowStartPageNumber;
				
				
				$this->page = $this->pdf->pages[$currentPageNumber];
				//error_log("currentPageNumber: $currentPageNumber");
				//styling
				$this->page->setFont($cell->getStyle()->getFont(), $cell->getStyle()->getFontSize());
				$this->page->setLineColor($cell->getStyle()->getColor());
				$this->page->setFillColor($cell->getStyle()->getColor());
				
				if(isset($this->colWidth[$i])){
					$colWidth = $this->colWidth[$i];
				}else{
					$colWidth = $this->defaultColumnWidth;
				}
				
				if($cell->hasImage()){
					/* @var $afbeelding \Afbeelding */
					$afbeelding = $cell->value;
					
					$factor = $afbeelding->Height()/$afbeelding->Width();
					$height = $colWidth * $factor;
					
					$file = JV::tempFile('jpg');
					$data = file_get_contents($pref->webSite.$afbeelding->getUrl($colWidth * 4));
					file_put_contents($file, $data);
                                        $image = Image::imageWithPath($file);
					$this->page->drawImage($image, $this->pointsX($xOffset), $this->pointsY($cellYOffset + 2 + $height - $row->rowHeight), $this->pointsX($xOffset + $colWidth), $this->pointsY($cellYOffset + 2 - $row->rowHeight));
					
					
					
					if($height + 2 > $row->rowHeight){
						$cellYOffset += $height + 2;
					}else{
						$cellYOffset += $row->rowHeight;
					}
					
				}else{
					$explode = explode("\n", trim($cell->value));
					
					foreach($explode as $value){
						$value = trim($value);
						
						$textWidth = PdfExtension::getTextWidth($value, $this->page, $cell->getStyle()->getFontSize(), 'UTF-8');//dit is in points
						
						//TODO: als $textWidth > is dan $colWidth, dan splitsen in woorden en
						//error_log("colWidth: ".$colWidth);
						//error_log("textWidth(mm): ".self::mm($textWidth));
						if(self::mm($textWidth) > $colWidth){
							//error_log("alinea splitsen.");
							//zin te lang voor de cell. splitsen in regels.
							//error_log($value);
							$woorden = explode(" ", $value);
							$regels = array();
							$regel = $woorden[0];
							for($c = 1;$c < count($woorden);$c++){
								
								$woord = $woorden[$c];
								//error_log("woord: $woord");
								$regelWidth = self::mm(PdfExtension::getTextWidth($regel." ".$woord, $this->page, $cell->getStyle()->getFontSize(), 'UTF-8'));
								if($regelWidth < $colWidth){
									$regel .= " $woord";
								}else{
									$regels[] = $regel;
									$regel = "$woord";
									
								}
							}
							$regels[] = $regel;
						}else{
							$regels = array($value);
						}
						
						$halfSpaceWidth = PdfExtension::getTextWidth(" ", $this->page, $cell->getStyle()->getFontSize(), 'UTF-8')/2;
						foreach($regels as $regel){
							
							if($cell->getStyle()->getAlign() == PdfTableCellStyle::right){
								$this->page->drawText($regel, $this->pointsX($colWidth + $xOffset) - $textWidth, $this->pointsY($cellYOffset), 'UTF-8');
								
								
							}else{
								$parts = explode("\u{2009}", $regel);
								$partWidthOffset = 0;
								foreach($parts as $part){
									
									$this->page->drawText($part, $this->pointsX($xOffset) + $partWidthOffset, $this->pointsY($cellYOffset), 'UTF-8');
									$partWidth = PdfExtension::getTextWidth($part, $this->page, $cell->getStyle()->getFontSize(), 'UTF-8');
									$partWidthOffset += $partWidth + $halfSpaceWidth ;
								}
								
								
								//$this->page->drawText($regel, $this->pointsX($xOffset), $this->pointsY($cellYOffset), 'UTF-8');
								//error_log("regel: $regel");
								
								//error_log("x = {$xOffset}, y = {$cellYOffset}, this->y = {$this->y}");
								
							}
							
							$cellYOffset += $row->rowHeight;
							
							
							if(self::pointsY($cellYOffset) < self::points(30)){
								//indien minder dan 30 mm marge aan onderkant:
								
								$currentPageNumber++;
								if($currentPageNumber > $lastPageNumber){
									$lastPageNumber = $currentPageNumber;
								}
								if($currentPageNumber > $cellHighestPage){
									$cellHighestPage = $currentPageNumber;
								}
								$this->setCurrentPageByNumber($currentPageNumber);
								
								$this->page->setFont($this->defaultFont, $this->defaultFontSize);
								
								$cellYOffset = $this->marginTop;
								
								if(!isset($yBiggestOffset[$currentPageNumber])){
									$yBiggestOffset[$currentPageNumber] = $this->marginTop;
								}
								
								
							}
							
						}//end foreach regel
					}//end foreach explode
				}
				$this->page->setLineColor($previousLineColor);
				$this->page->setFillColor($previousFillColor);
				
				
				if(!isset($yBiggestOffset[$currentPageNumber])){
					$yBiggestOffset[$currentPageNumber] = $this->marginTop;
				}
				
				if($cellYOffset > $yBiggestOffset[$currentPageNumber]){
					$yBiggestOffset[$currentPageNumber] = $cellYOffset;
				}
				
				//error_log("page: {$currentPageNumber} cell Yoffset: {$cellYOffset} | biggest: {$yBiggestOffset[$currentPageNumber]} | value: {$cell->value}");
				
				$xOffset += $colWidth;
				$i++;
				
				
			}//end cell foreach
			
			
			
//			if(!isset($yBiggestOffset[$lastPageNumber])){
//				$yBiggestOffset[$lastPageNumber] = $this->marginTop;
//			}
			
			//ErrorLogColor::log("einde rij: yOffset{$yOffset}, curPageNr: {$currentPageNumber}; lastPageNr: {$lastPageNumber}", 'red');
			//$yOffset = $yBiggestOffset[count($yBiggestOffset)-1];
			
			
			if(self::pointsY($yBiggestOffset[$currentPageNumber]) < self::points(30)){
				
				//indien minder dan 30 mm marge aan onderkant:
				$currentPageNumber++;
				
				if($currentPageNumber > $lastPageNumber){
					$lastPageNumber = $currentPageNumber;
				}
				
				$this->setCurrentPageByNumber($currentPageNumber);
				if(!isset($yBiggestOffset[$currentPageNumber])){
					$yBiggestOffset[$currentPageNumber] = $this->marginTop;
				}
				
				$this->page->setFont($this->defaultFont, $this->defaultFontSize);
				
			}
			
			if($currentPageNumber < $cellHighestPage){
				$currentPageNumber = $cellHighestPage;
			}
			$rowYOffset = $yBiggestOffset[$currentPageNumber];
			

//			//FIXME: Dit weer verwijderen:
//			if($rowNumber > 68){
//				break;
//			}
//			$rowNumber++;
		}
		$page = $this->page = $this->pdf->pages[$lastPageNumber];
		return $yBiggestOffset[$lastPageNumber];
	}
	
	public function setCurrentPageByNumber($currentPageNumber){
		jvlog("setCurrentPageByNumber($currentPageNumber)", 'magenta');
		if(isset($this->pdf->pages[$currentPageNumber])){
			error_log("de pdf pagina bestaat al");
			$this->page = $this->pdf->pages[$currentPageNumber];
		}else{
			jvlog("er dient een nieuwe pdf pagina aangemaakt te worden: $currentPageNumber | ".count($this->pdf->pages), 'blue');
			$this->page = $this->pdf->pages[$currentPageNumber] = $this->pdf->newPage(LaminasPage::SIZE_A4);
			
			jvlog("na aanmaken aantal: | ".count($this->pdf->pages), 'blue');
		}
	}
	
	public static function points($mm){
		return $mm * 2.83465;
	}
	
	public static function mm($points){
		return $points / 2.83465;
	}
	
	/**
	 * @param $mm float in mm
	 *
	 * @return float in points
	 */
	public function pointsX($mm){
		return self::points($mm);
	}
	
	
	/**
	 * @param $mm float in mm
	 *
	 * @return float in points
	 */
	public function pointsY($mm){
		//if(!isset($this->page)){
		//	throw new Exception("Een Y hoogte kan alleen worden bepaald als er een page is, omdat pdf vanuit de linker benedenhoek werkt en wij vanaf linksboven.");
		//}
		return $this->page->getHeight() - self::points($mm);
	}
	
	

	
	
	
}

class PdfTableRow {
	/**
	 * @var PdfTableCell[]
	 */
	var $cells = array();
	
	var $rowHeight;
	
	var $defaultFont;
	var $defaultFontSize;
	
	/**
	 * @param $value string
	 *
	 * @return PdfTableCell
	 */
	public function addCell($value){
		$cell = new PdfTableCell();
		$cell->setFontSize($this->defaultFontSize);
		$cell->value = $value;
		$this->cells[] = $cell;
		return $cell;
	}
	
	public function setBold(){
		foreach($this->cells as $cell){
			$cell->setBold();
		}
	}
	
	
	/**
	 * Convinience functie zodat we gemakkelijk string cells achter elkaar kunnen opgeven.
	 * @param $array string[]
	 */
	public function addCells($array){
		$cells = array();
		foreach($array as $str){
			$cells[] = $this->addCell($str);
		}
		return $cells;
	}
	
	public function addBedragCell($value, $decimals = 0){
		$cell = $this->addCell(self::euro($value, $decimals));
		
		$cell->setAlignRight();
		if($value < 0){
			$cell->setColor("#cc0000");
		}
		return $cell;
	}
	
	public static function euro($bedrag, $decimals = 0){
		if($decimals > 0){
			$decimals = true;
		}else{
			$decimals = false;
		}
		return Database::euro($bedrag, '€', $decimals);
		//return "€ ".round($bedrag, $decimals);
	}
	
	public function setRowHeight($rowHeight){
		$this->rowHeight = $rowHeight;
		return $this;
	}
	
	
	/**
	 * @param $colNr int
	 *
	 * @return mixed|PdfTableCell
	 */
	public function getCell($colNr){
		if(isset($this->cells[$colNr])){
			return $this->cells[$colNr];
		}
		$cell = new PdfTableCell();
		$this->cells[$colNr] = $cell;
		return $cell;
	}
}

class PdfTableCell {
	/**
	 * @var string
	 */
	var $value;
	
	/**
	 * @var PdfTableCellStyle
	 */
	protected $style;
	
	protected $_hasImage = false;
	public function hasImage(){
		return $this->_hasImage;
	}
	
	public function setImage(Afbeelding $image){
		$this->_hasImage = true;
		$this->value = $image;
	}
	
	/**
	 * @param string $value
	 *
	 * @return PdfTableCell
	 */
	public function setValue(string $value):PdfTableCell{
		$this->value = $value;
		
		return $this;
	}
	
	public function getStyle(){
		if(isset($this->style)){
			return $this->style;
		}
		return new PdfTableCellStyle();
	}
	
	
	/**
	 * @param PdfTableCellStyle|array $style
	 *
	 * @return PdfTableCell
	 */
	public function setStyle($style):PdfTableCell{
		if(is_array($style)){
			if(!isset($this->style)){
				$this->style = new PdfTableCellStyle();
			}
			foreach($style as $key => $value){
				$this->style->$key = $value;
			}
			
		}else{
			$this->style = $style;
		}
		
		return $this;
	}
	
	public function setBold($bool = true){
		$this->setStyle([PdfTableCellStyle::bold => $bool]);
		return $this;
	}
	
	public function setItalic($bool = true){
		$this->setStyle([PdfTableCellStyle::italic => $bool]);
		return $this;
	}
	
	public function setColor($color){
		$this->setStyle([PdfTableCellStyle::color => $color]);
		return $this;
	}
	
	public function setAlignLeft(){
		$this->setStyle([PdfTableCellStyle::align => PdfTableCellStyle::left]);
		return $this;
	}
	
	public function setAlignRight(){
		$this->setStyle([PdfTableCellStyle::align => PdfTableCellStyle::right]);
		return $this;
	}
	
	public function setAlignCenter(){
		$this->setStyle([PdfTableCellStyle::align => PdfTableCellStyle::center]);
		return $this;
	}
	
	
	
	/**
	 * @param $size float points
	 *
	 * @return $this
	 */
	public function setFontSize($size){
		$this->setStyle([PdfTableCellStyle::fontSize => $size]);
		return $this;
	}
	
}

class PdfTableCellStyle {
	
	const bold = "bold";
	const fontSize = "fontSize";
	const font = "font";
	const italic = "italic";
	const color = "color";
	
	const align = "align";
	const left = "left";
	const right = "right";
	const center = "center";
	
	var $bold = false;
	var $fontSize;
	var $font;
	var $italic = false;
	var $color = '#000000';
	var $align = PdfTableCellStyle::left;
	
	static $defaultFont = Font::FONT_HELVETICA;
	static $defaultsize = 11;//points
	
	
	/**
	 * @return float points;
	 */
	public function getFontSize(){
		if(isset($this->fontSize)){
			return $this->fontSize;
		}
		return self::$defaultsize;
	}
	
	public function getAlign(){
		return $this->align;
	}
	
	public function getFont(){
		if(!isset($this->font)){
			$this->font = static::$defaultFont;
		}
		
		try{
			if($this->font == Font::FONT_HELVETICA){
				if($this->bold === true && $this->italic === false){
					return Font::fontWithName(Font::FONT_HELVETICA_BOLD);
				}elseif($this->bold === true && $this->italic === true){
					return Font::fontWithName(Font::FONT_HELVETICA_BOLD_ITALIC);
				}elseif($this->bold === false && $this->italic === true){
					return Font::fontWithName(Font::FONT_HELVETICA_ITALIC);
				}else{
					return Font::fontWithName(Font::FONT_HELVETICA);
				}
				
			}
		}catch(\Exception $e){
		
		}
		return $this->font;
	}
	
	
	/**
	 * @return Html
	 * @throws \Exception
	 */
	public function getColor(){
		$color =  new Html($this->color);
		return $color;
	}
	
	
	
}





