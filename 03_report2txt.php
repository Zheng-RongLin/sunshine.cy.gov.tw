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
    $txtFile = "{$txtPath}/{$line[1]}.csv";

    for($i = 3; $i < 8; $i++) {
      if(!empty($line[$i])) {
        $p = pathinfo($line[$i]);
        $iCount = $i - 2;
        $targetFile = "{$reportPath}/{$line[1]}-{$iCount}.{$p['extension']}";
        if (!file_exists($targetFile) || filesize($targetFile) === 0) {
          file_put_contents($targetFile, file_get_contents($line[$i]));
        }
      }
    }

    echo "processing {$txtFile}\n";
    if (!file_exists($reportFile) || filesize($reportFile) === 0) {
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
    if($pathinfo['extension'] === 'doc') {
      //convert to pdf first
      exec("/usr/bin/soffice --headless --convert-to pdf {$reportFile}");
      foreach(glob('*.pdf') AS $reportFile) {
        exec("/usr/bin/java -jar /home/kiang/programs/tabula-java/tabula-1.0.2-jar-with-dependencies.jar -l {$reportFile} > {$txtFile}");
        unlink($reportFile);
      }
    } else {
      exec("/usr/bin/java -jar /home/kiang/programs/tabula-java/tabula-1.0.2-jar-with-dependencies.jar -l {$reportFile} > {$txtFile}");
    }
}
