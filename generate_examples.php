<?php

$exampleDir = __DIR__ . '/examples';
$exampleScripts = glob($exampleDir . '/[0-9][0-9][0-9]*.php');

foreach ($exampleScripts as $script) {
	$cmd = 'php ' . escapeshellarg($script);
	$return = null;
	passthru($cmd, $return);

	if ($return !== 0) {
		fwrite(STDERR, "Error executing {$script}\n");
		continue;
	}

	$targetName = preg_replace('/\.php$/', '.pdf', basename($script));
	$outputFile = $exampleDir . '/output.pdf';
	if (file_exists($outputFile)) {
		rename($outputFile, $exampleDir . '/' . $targetName);
		echo "Generated {$targetName}\n";
	} else {
		fwrite(STDERR, "Output file not found for {$script}\n");
	}
}
