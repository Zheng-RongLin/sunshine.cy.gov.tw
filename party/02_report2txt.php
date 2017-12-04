<?php

$tmpPath = dirname(__DIR__) . '/tmp';
$txtPath = __DIR__ . '/report2txt';
$reportPath = __DIR__ . '/report';

$errors = array();
$fh = fopen('party_reports.csv', 'r');
while ($line = fgetcsv($fh, 2048)) {
    $pathinfo = pathinfo($line[6]);
    $reportFile = "{$reportPath}/{$line[0]}_{$line[1]}.{$pathinfo['extension']}";
    $txtFile = "{$txtPath}/{$line[0]}_{$line[1]}.txt";

    echo "processing {$txtFile}\n";
    if (!file_exists($reportFile) || filesize($reportFile) === 0) {
        file_put_contents($reportFile, file_get_contents($line[6]));
    }
    if (filesize($reportFile) === 0) {
        $errors[] = array(
            'download error',
            implode('|', $line),
        );
        unlink($reportFile);
        continue;
    }
    if (!file_exists($txtFile) || filesize($txtFile) === 0) {
        exec("abiword --to=txt {$reportFile} -o {$txtFile}");
    }
    if (filesize($txtFile) === 0) {
      $line[] = $reportFile;
        $errors[] = array(
            'convert error',
            implode('|', $line),
        );
        continue;
    }
}
$fh = fopen('po_reports.csv', 'r');
while ($line = fgetcsv($fh, 2048)) {
    $pathinfo = pathinfo($line[6]);
    $reportFile = "{$reportPath}/{$line[0]}_{$line[1]}.{$pathinfo['extension']}";
    $txtFile = "{$txtPath}/{$line[0]}_{$line[1]}.txt";

    echo "processing {$txtFile}\n";
    if (!file_exists($reportFile) || filesize($reportFile) === 0) {
        file_put_contents($reportFile, file_get_contents($line[6]));
    }
    if (filesize($reportFile) === 0) {
        $errors[] = array(
            'download error',
            implode('|', $line),
        );
        unlink($reportFile);
        continue;
    }
    if (!file_exists($txtFile) || filesize($txtFile) === 0) {
        exec("abiword --to=txt {$reportFile} -o {$txtFile}");
    }
    if (filesize($txtFile) === 0) {
      $line[] = $reportFile;
        $errors[] = array(
            'convert error',
            implode('|', $line),
        );
        continue;
    }
}

print_r($errors);
