<?php

$tmpPath = dirname(__FILE__) . '/tmp';
$txtPath = dirname(__FILE__) . '/report2txt';
$reportPath = dirname(__FILE__) . '/report';

$errors = array();
$fh = fopen('reports.csv', 'r');
while ($line = fgetcsv($fh, 2048)) {
    $pathinfo = pathinfo($line[2]);
    $line[1] = str_replace(array('(', ')'), array('_', '_'), $line[1]);
    $reportFile = "{$reportPath}/{$line[1]}.{$pathinfo['extension']}";
    $txtFile = "{$txtPath}/{$line[1]}.txt";

    echo "processing {$txtFile}\n";
    if (!file_exists($reportFile)) {
        file_put_contents($reportFile, file_get_contents($line[2]));
    }
    if (filesize($reportFile) === 0) {
        $errors[] = array(
            'download error',
            implode('|', $line),
        );
        unlink($reportFile);
        continue;
    }
    if (!file_exists($txtFile)) {
        exec("abiword --to=txt {$reportFile} -o {$txtFile}");
    }
    if (filesize($txtFile) === 0) {
        $errors[] = array(
            'convert error',
            implode('|', $line),
        );
        continue;
    }
}

print_r($errors);
