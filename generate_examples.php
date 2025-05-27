<?php

$exampleDir = __DIR__ . '/examples';
$exampleScripts = glob($exampleDir . '/[0-9][0-9][0-9]*.php');

foreach ($exampleScripts as $script) {
    $targetName = preg_replace('/\.php$/', '.pdf', basename($script));
    $targetFile = $exampleDir . '/' . $targetName;
    if (file_exists($targetFile)) {
        echo "Skipping {$targetName}, already exists\n";
        continue;
    }

	$cmd = 'php ' . escapeshellarg($script);
	$return = null;
	passthru($cmd, $return);

	if ($return !== 0) {
		fwrite(STDERR, "Error executing {$script}\n");
		continue;
	}

	$outputFile = $exampleDir . '/output.pdf';
	if (file_exists($outputFile)) {
		rename($outputFile, $targetFile);
		echo "Generated {$targetName}\n";
	} else {
		fwrite(STDERR, "Output file not found for {$script}\n");
	}
}
