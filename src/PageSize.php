<?php
namespace JVelthuis\JVPdf;

enum PageSize: string
{
	case A4_Portrait = '210:297';//mm
	case A4_Landscape = '297:210';//mm
	
	
	// Methode om de afmetingen van de enumwaarde te verkrijgen
	public function dimensions(): array
	{
		return self::parseDimensions($this->value);
	}
	

	// Statische methode om afmetingen te parsen uit een string
	public static function parseDimensions(string|PageSize $size): array
	{
		if($size instanceof PageSize) {
			$size = $size->value;
		}
		
		$dimensions = explode(':', $size);
		if (count($dimensions) === 2) {
			return $dimensions;
		}
		throw new \InvalidArgumentException("Invalid size: $size");
	}
	
	public static function orientation(string|PageSize $size):string {
		$dimensions = self::parseDimensions($size);
		if($dimensions[0] >= $dimensions[1]) {
			return 'L';
		}
		return 'P';
	}
}
