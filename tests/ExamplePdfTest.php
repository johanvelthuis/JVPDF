<?php

namespace JVelthuis\JVPdf\Tests;

use PHPUnit\Framework\TestCase;

class ExamplePdfTest extends TestCase
{
	

	/**
	 * Vergelijk de gerenderde PNG‑beelden van twee PDF’s.
	 */
	private function comparePdfRendering(string $expected, string $generated): void
	{
		$tmp = sys_get_temp_dir() . '/pdfcmp_' . uniqid();
		if (!mkdir($tmp) && !is_dir($tmp)) {
			$this->fail('Kon temp‑map niet aanmaken');
		}

		$gsArgs = '-dSAFER -dBATCH -dNOPAUSE -sDEVICE=pngalpha -r150';
		$cmdExpected  = sprintf('gs %s -sOutputFile=%s/exp-%%03d.png %s', $gsArgs, escapeshellarg($tmp), escapeshellarg($expected));
		$cmdGenerated = sprintf('gs %s -sOutputFile=%s/gen-%%03d.png %s', $gsArgs, escapeshellarg($tmp), escapeshellarg($generated));

		exec($cmdExpected, $_, $ret);
		$this->assertSame(0, $ret, 'Ghostscript faalde op expected PDF');

		exec($cmdGenerated, $_, $ret);
		$this->assertSame(0, $ret, 'Ghostscript faalde op generated PDF');

		$expPages = glob($tmp . '/exp-*.png');
		$genPages = glob($tmp . '/gen-*.png');
		sort($expPages);
		sort($genPages);

		$this->assertSameSize($expPages, $genPages, basename($expected) . ' pagina‑aantal verschilt');

		foreach ($expPages as $i => $expPng) {
			$genPng = $genPages[$i];
			$this->assertSame(
				hash_file('sha256', $expPng),
				hash_file('sha256', $genPng),
				basename($expected) . ' pagina ' . ($i + 1) . ' wijkt af'
			);
		}

		array_map('unlink', array_merge($expPages, $genPages));
		rmdir($tmp);
	}
	
	/**
	 * @dataProvider pdfScripts
	 */
	public function testPdfLooksCorrect(string $script): void
	{
		$exampleDir = dirname($script);
		$expected   = preg_replace('/\.php$/', '.pdf', $script);

		exec('php ' . escapeshellarg($script), $out, $rc);
		$this->assertSame(0, $rc, "Run $script failed");

		$generated = $exampleDir . '/output.pdf';
		$this->assertFileExists($generated);

		if (!file_exists($expected)) {
			rename($generated, $expected);
			$this->markTestIncomplete(basename($expected) . ' baseline created');
		}

		$this->comparePdfRendering($expected, $generated);
		unlink($generated);
	}

	public static function pdfScripts(): iterable
	{
		$dir = realpath(__DIR__ . '/../examples');
		foreach (glob($dir . '/[0-9][0-9][0-9]*.php') as $script) {
			yield basename($script) => [$script]; // test‑naam == script‑naam
		}
	}
}
