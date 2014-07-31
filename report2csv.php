<?php

$txtPath = dirname(__FILE__) . '/report2txt';
$listFh = fopen(dirname(__FILE__) . '/report2csv.csv', 'w');
$tokens = array(
    '個人捐贈收入' => 0,
    '營利事業捐贈收入' => 0,
    '政黨捐贈收入' => 0,
    '人民團體捐贈收入' => 0,
    '匿名捐贈收入' => 0,
    '其他收入' => 0,
    '人事費用支出' => 0,
    '宣傳支出' => 0,
    '租用宣傳車輛支出' => 0,
    '租用競選辦事處支出' => 0,
    '集會支出' => 0,
    '交通旅運支出' => 0,
    '雜支支出' => 0,
    '返還捐贈支出' => 0,
    '繳庫支出' => 0,
    '公共關係費用支出' => 0,);
$labelLine = false;
foreach (glob($txtPath . '/*.txt') AS $file) {
    $account = basename($file);
    $account = str_replace(array('擬選人'), array('擬參選人'), $account);
    $accountParts = explode('擬參選人', $account);
    $accountParts2 = explode('政治獻金專戶', $accountParts[1]);
    $data = array_merge(array(
        '選舉類型' => $accountParts[0],
        '擬參選人' => $accountParts2[0],
            ), $tokens);
    $fh = fopen($file, 'r');
    $currentToken = false;
    while ($line = fgets($fh, 1024)) {
        $line = trim($line);
        if (false === $currentToken) {
            if (isset($tokens[$line])) {
                $currentToken = $line;
            }
        } else {
            $line = str_replace(array(','), array(''), $line);
            $data[$currentToken] = $line;
            $currentToken = false;
        }
    }
    if (count($data) !== 18) {
        print_r($data);
        exit();
    }
    fclose($fh);
    if (false === $labelLine) {
        fputcsv($listFh, array_keys($data));
        $labelLine = true;
    }
    fputcsv($listFh, $data);
}
fclose($listFh);
