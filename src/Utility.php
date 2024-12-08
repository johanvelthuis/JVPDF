<?php

namespace JVelthuis\JVPdf;

use LaminasPdf\Resource\Font\AbstractFont;
use LaminasPdf\BinaryParser\Font\OpenType\AbstractOpenType;
use LaminasPdf\Resource\Font\CidFont\AbstractCidFont;
use LaminasPdf\Resource\Font\Type0;

class Utility {
	
	
	/**
	 * Converteert een opgegeven pad naar een absoluut pad.
	 *
	 * @param string $path Het pad dat geconverteerd moet worden.
	 * @return string Het absolute pad.
	 */
	public static function absolutePath(string $path): string
	{
		// Controleer of het pad al absoluut is
		if ($path[0] === '/' || $path[0] === '\\' || preg_match('/^[a-zA-Z]:[\/\\\\]/', $path)) {
			return $path; // Absoluut pad
		}
		
		// Verkrijg het pad van het initiële script dat door de gebruiker is aangeroepen
		$files = get_included_files();
		$initialScriptDir = dirname($files[0]);
		
		// Combineer het initiële scriptpad met het relatieve pad
		$absolutePath = $initialScriptDir . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
		
		// Normaliseer en retourneer het pad
		return realpath($absolutePath) ?: $absolutePath;
	}
	
	public static function getTextWidth(string $text, AbstractFont|AbstractOpenType $font, float $fontSize, string $encoding = 'UTF-8'): float
	{
		$unitsPerEm = $font->getUnitsPerEm();
		$totalWidth = 0;
		$glyphs = [];
		
		
		//echo "Fontname: {$font->getFontName(\LaminasPdf\Font::NAME_FULL, 'nl_NL')}\n";
		
		if ($font instanceof AbstractCidFont) {
			error_log("font is instance of CidFont");
			// Get the mapping from Unicode to CID
			// Check if the font has a method to map characters
			if (method_exists($font, 'glyphNumbersForCharacters')) {
				$glyphs = $font->glyphNumbersForCharacters(mb_str_split($text, 1, $encoding));
			} else {
				throw new \Exception("Font does not support mapping characters to CIDs.");
			}
		}elseif ($font instanceof  Type0){
			error_log("font is instance of Type0");
			$glyphs = $font->glyphNumbersForCharacters(mb_str_split($text, 1, $encoding));
		}else {
			error_log("font is blijkbaar wat anders".get_class($font));
			//Dit blijkt dus in ons arial voorbeeld een Type0

//			if (method_exists($font, 'glyphNumberForCharacter')) {
//
//				// Use getGlyphForCharacter for efficient mapping
//				$length = mb_strlen($text, $encoding);
//				for ($i = 0; $i < $length; $i++) {
//					$char = mb_substr($text, $i, 1, $encoding);
//					$glyphs[] = $font->glyphNumberForCharacter($char);
//				}
//			}
			
			$length = mb_strlen($text, $encoding);
			for ($i = 0; $i < $length; $i++) {
				$char = mb_substr($text, $i, 1, $encoding);
				$glyphs[] = $font->getGlyphForCharacter($char);
			}

		}
		
//		if (method_exists($font, 'glyphNumbersForCharacters')) {
//			$glyphs = $font->glyphNumbersForCharacters(mb_str_split($text, 1, $encoding));
//		} else {
//			throw new \Exception("Font does not support mapping characters to CIDs.");
//		}
		//var_dump($glyphs);
		
		// Get the widths of the glyphs
		$glyphWidths = $font->widthsForGlyphs($glyphs);
		
		$glyphCount = count($glyphs);
		
		for ($j = 0; $j < $glyphCount; $j++) {
			$totalWidth += $glyphWidths[$j];
			
			// Add kerning if this is not the last glyph
			if ($j < $glyphCount - 1) {
				if(is_int($glyphs[$j]) && is_int($glyphs[$j + 1])){
					$kerningAdjustment = $font->getKerningAdjustment($glyphs[$j], $glyphs[$j + 1]);
					error_log("kerningAdjustment: $kerningAdjustment");
					$totalWidth += $kerningAdjustment;
					
				}
			}
		}
		
		$textWidth = ($totalWidth / $unitsPerEm) * $fontSize;
		
		return $textWidth;
	}

	
	
}
