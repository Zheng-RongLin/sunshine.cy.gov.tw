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
    switch ($account) {
        case '第11屆臺北市議員擬參選人陳玉梅政治獻金專戶.txt':
            $data['個人捐贈收入'] = 19139819;
            $data['營利事業捐贈收入'] = 13703000;
            $data['人民團體捐贈收入'] = 403200;
            $data['其他收入'] = 4520;
            $data['人事費用支出'] = 2192746;
            $data['宣傳支出'] = 2626673;
            $data['租用宣傳車輛支出'] = 795523;
            $data['租用競選辦事處支出'] = 1256676;
            $data['集會支出'] = 1230082;
            $data['交通旅運支出'] = 191669;
            $data['雜支支出'] = 1094280;
            $data['返還捐贈支出'] = 990000;
            $data['繳庫支出'] = 100000;
            $data['公共關係費用支出'] = 1633400;
            break;
        case '第1屆新北市議員擬參選人劉美芳政治獻金專戶.txt':
            $data['個人捐贈收入'] = 902000;
            $data['營利事業捐贈收入'] = 656000;
            $data['政黨捐贈收入'] = 200000;
            $data['匿名捐贈收入'] = 36164;
            $data['人事費用支出'] = 66164;
            $data['宣傳支出'] = 1538000;
            $data['租用宣傳車輛支出'] = 100000;
            $data['雜支支出'] = 90000;
            break;
        case '第1屆臺中市議員擬參選人曾朝榮政治獻金專戶.txt':
            $data['個人捐贈收入'] = 8454100;
            $data['營利事業捐贈收入'] = 2282200;
            $data['人民團體捐贈收入'] = 40100;
            $data['匿名捐贈收入'] = 7888;
            $data['其他收入'] = 2;
            $data['人事費用支出'] = 3178600;
            $data['宣傳支出'] = 3523627;
            $data['租用宣傳車輛支出'] = 160550;
            $data['租用競選辦事處支出'] = 295693;
            $data['集會支出'] = 627463;
            $data['交通旅運支出'] = 182180;
            $data['雜支支出'] = 1134937;
            $data['公共關係費用支出'] = 32450;
            break;
        case '第1屆高雄市議員擬參選人鄭光峰政治獻金專戶.txt':
            $data['個人捐贈收入'] = 6174600;
            $data['營利事業捐贈收入'] = 2861500;
            $data['政黨捐贈收入'] = 20000;
            $data['人民團體捐贈收入'] = 76800;
            $data['匿名捐贈收入'] = 52900;
            $data['人事費用支出'] = 2329562;
            $data['宣傳支出'] = 5302646;
            $data['租用宣傳車輛支出'] = 81000;
            $data['租用競選辦事處支出'] = 27075;
            $data['集會支出'] = 1010054;
            $data['交通旅運支出'] = 142301;
            $data['雜支支出'] = 603863;
            $data['公共關係費用支出'] = 93947;
            break;
        default:
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
    }

    if (false === $labelLine) {
        fputcsv($listFh, array_keys($data));
        $labelLine = true;
    }
    fputcsv($listFh, $data);
}
fclose($listFh);
