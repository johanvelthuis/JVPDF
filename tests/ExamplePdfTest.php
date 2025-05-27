<?php

namespace JVelthuis\JVPdf\Tests;

use PHPUnit\Framework\TestCase;

class ExamplePdfTest extends TestCase
{
	public function testExamplePdfs()
	{
		$exampleDir = realpath(__DIR__ . '/../examples');
		$scripts = glob($exampleDir . '/[0-9][0-9][0-9]*.php');

		foreach ($scripts as $script) {
			$expected = preg_replace('/\.php$/', '.pdf', $script);
			$cmd = 'php ' . escapeshellarg($script);
			exec($cmd, $output, $result);
			$this->assertSame(0, $result, "Failed executing $script");

			$generated = $exampleDir . '/output.pdf';
			$this->assertFileExists($generated);

			if (!file_exists($expected)) {
				rename($generated, $expected);
				$this->markTestIncomplete(basename($expected) . ' baseline created');
				continue;
			}

			$this->assertFileEquals($expected, $generated);
			unlink($generated);
		}
	}
}
