<?php

namespace JVelthuis\JVPdf;

use JVelthuis\JVPdf\Unit;

class UnitConverter
{
	/**
	 * Convert coordinates based on unit and origin settings.
	 *
	 * @param float $x
	 * @param float $y
	 * @param array $pageSize [width, height] in points
	 * @param Unit $unit
	 * @param bool $useTopLeftOrigin
         * @return float[] Converted coordinates as [x, y] in points
	 */
	public static function convertCoordinates(float $x, float $y, array $pageSize, Unit $unit, bool $useTopLeftOrigin): array
	{
		// Convert units to points
		$x = self::convertUnit($x, $unit);
		$y = self::convertUnit($y, $unit);
		
		// Adjust Y coordinate for top-left origin if needed
		if ($useTopLeftOrigin) {
			$y = $pageSize[1] - $y; // pageSize[1] = page height
		}
		
                return [$x, $y];
	}
	
	/**
	 * Convert a value to points based on the specified unit.
	 *
	 * @param float $value
	 * @param Unit $unit
	 * @return float Value in points
	 */
	public static function convertUnit(float $value, Unit $unit): float
	{
		return match ($unit) {
			Unit::POINT => $value,
			Unit::MM => $value * 72 / 25.4, // Convert mm to points
			Unit::INCH => $value * 72,             // Convert inches to points
		};
	}
	
	/**
	 * Convert a value from points to the specified unit.
	 *
	 * @param float $value
	 * @param Unit $unit
	 * @return float Value in the specified unit
	 */
	public static function convertFromPoints(float $value, Unit $unit): float
	{
		return match ($unit) {
			Unit::POINT => $value,
			Unit::MM => $value * 25.4 / 72, // Convert points to mm
			Unit::INCH => $value / 72,      // Convert points to inches
		};
	}
	
	/**
	 * Convert a rectangle's coordinates and dimensions based on unit and origin settings.
	 *
	 * @param float $x
	 * @param float $y
	 * @param float $w
	 * @param float $h
	 * @param array $pageSize [width, height] in points
	 * @param Unit $unit
	 * @param bool $useTopLeftOrigin
	 * @return array Converted rectangle as [x, y, w, h] in points
	 */
	public static function convertRect(float $x, float $y, float $w, float $h, array $pageSize, Unit $unit, bool $useTopLeftOrigin): array
	{
		// Convert width and height to points
		$w = self::convertUnit($w, $unit);
		$h = self::convertUnit($h, $unit);
		
		// Convert x and y coordinates to points
		[$x, $y] = self::convertCoordinates($x, $y, $pageSize, $unit, $useTopLeftOrigin);
		
		// Adjust height for top-left origin
		if ($useTopLeftOrigin) {
			$y -= $h; // Move Y down by the height of the rectangle
		}
		
		return [$x, $y, $w, $h];
	}
}
