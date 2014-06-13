<?php
$fh = fopen('list_new.csv', 'r');
$result = array();
while($line = fgetcsv($fh, 2048)) {
    $parts = explode('擬參選人', $line[1]);
    if(!isset($result[$parts[0]])) {
        $result[$parts[0]] = array();
    }
    $result[$parts[0]][] = $line[0];
}
foreach($result AS $type => $names) {
    echo "[{$type}]\n\n";
    $counter = 0;
    foreach($names AS $name) {
        echo " {$name} ";
        if(++$counter % 6 === 0) {
            echo "\n";
        }
    }
    echo "\n\n";
}