<?php
$fh = fopen(__DIR__ . '/list_2018.csv', 'r');
$result = array();
while($line = fgetcsv($fh, 2048)) {
  $parts = preg_split('/(年|擬參選人|政治獻金專戶)/', $line[1]);
  $city = mb_substr($parts[1], 0, 3, 'utf-8');
  $position = mb_substr($parts[1], 3, null, 'utf-8');
  if(!isset($result[$position])) {
    $result[$position] = array();
  }
  if(!isset($result[$position][$city])) {
    $result[$position][$city] = array();
  }
  $result[$position][$city][] = $parts[2];
}

foreach($result AS $p => $d) {
  echo "{$p}\n\n";
  foreach($d AS $city => $names) {
    $count = count($names);
    echo "[{$city} - {$count}]\n";
    echo implode(', ', $names) . "\n";
  }
}
