<?php

namespace JVelthuis\JVPdf\Tests;

use PHPUnit\Framework\TestCase;
use JVelthuis\JVPdf\UnitConverter;
use JVelthuis\JVPdf\Unit;

class UnitConverterTest extends TestCase
{
    public function testConvertUnit()
    {
        $this->assertEquals(10, UnitConverter::convertUnit(10, Unit::POINT));
        $this->assertEqualsWithDelta(28.346, UnitConverter::convertUnit(10, Unit::MM), 0.001);
        $this->assertEquals(720, UnitConverter::convertUnit(10, Unit::INCH));
    }

    public function testConvertFromPoints()
    {
        $this->assertEquals(10, UnitConverter::convertFromPoints(10, Unit::POINT));
        $this->assertEqualsWithDelta(3.528, UnitConverter::convertFromPoints(10, Unit::MM), 0.001);
        $this->assertEqualsWithDelta(0.1389, UnitConverter::convertFromPoints(10, Unit::INCH), 0.001);
    }

    public function testConvertCoordinates()
    {
        $pageSize = [200, 300];

        $this->assertSame([10, 15], UnitConverter::convertCoordinates(10, 15, $pageSize, Unit::POINT, false));
        $this->assertSame([10, 285], UnitConverter::convertCoordinates(10, 15, $pageSize, Unit::POINT, true));

        $pageSize = [595, 842];
        $this->assertSame([72, 770], UnitConverter::convertCoordinates(25.4, 25.4, $pageSize, Unit::MM, true));
    }
}
