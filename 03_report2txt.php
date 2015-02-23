<?php
$tmpPath = dirname(__FILE__) . '/tmp';
$txtPath = dirname(__FILE__) . '/report2txt';

$errors = array();
$fh = fopen('reports.csv', 'r');
while($line = fgetcsv($fh, 2048)) {
    $line[1] = str_replace(array('(', ')'), array('_', '_'), $line[1]);
    $tmpFile = $tmpPath . '/' . md5($line[2]) . '.doc';
    $txtFile = "{$txtPath}/{$line[1]}.txt";
    if(!file_exists($tmpFile)) {
        file_put_contents($tmpFile, file_get_contents($line[2]));
    }
    if(filesize($tmpFile) === 0) {
        $errors[] = array(
            'download error',
            implode('|', $line),
        );
        continue;
    }
    if(!file_exists($txtFile)) {
        exec("abiword --to=txt {$tmpFile} -o {$txtFile}");
    }
    if(filesize($txtFile) === 0) {
        $errors[] = array(
            'convert error',
            implode('|', $line),
        );
        continue;
    }
}

print_r($errors);