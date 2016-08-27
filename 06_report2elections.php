<?php

$fh = fopen('report2csv.csv', 'r');
/*
  Array
  (
  [0] => 選舉類型
  [1] => 擬參選人
  [2] => 個人捐贈收入
  [3] => 營利事業捐贈收入
  [4] => 政黨捐贈收入
  [5] => 人民團體捐贈收入
  [6] => 匿名捐贈收入
  [7] => 其他收入
  [8] => 人事費用支出
  [9] => 宣傳支出
  [10] => 租用宣傳車輛支出
  [11] => 租用競選辦事處支出
  [12] => 集會支出
  [13] => 交通旅運支出
  [14] => 雜支支出
  [15] => 返還捐贈支出
  [16] => 繳庫支出
  [17] => 公共關係費用支出
  )
 */
$keys = array(
    '里長' => '村里長',
    '村長' => '村里長',
    '村_里_長' => '村里長',
    '鄉鎮市)代表' => '鄉鎮市民代表',
    '鄉鎮市_代表' => '鄉鎮市民代表',
    '鄉_鎮、市_民代表' => '鄉鎮市民代表',
    '鄉民代表' => '鄉鎮市民代表',
    '市民代表' => '鄉鎮市民代表',
    '鎮民代表' => '鄉鎮市民代表',
    '鄉長' => '鄉鎮市長',
    '市長' => '市長',
    '鎮長' => '鄉鎮市長',
    '縣長' => '縣市長',
    '議員' => '議員',
    '立法委員' => '立法委員',
    '總統、副總統' => '總統',
);
$tmpPath = __DIR__ . '/tmp/elections';
if (!file_exists($tmpPath)) {
    mkdir($tmpPath, 0777, true);
}
$result = array();
$oFh = fopen(__DIR__ . '/report2elections.csv', 'w');
fputcsv($oFh, array_merge(array('election_id', 'election', 'candidate_id'), fgetcsv($fh, 2048)));
while ($line = fgetcsv($fh, 2048)) {
    if (false === strpos($line[0], '105年')) {
        continue;
    }
    $keyFound = false;
    $tmpFile = $tmpPath . '/' . $line[1] . '.json';
    if (!file_exists($tmpFile)) {
        error_log('downloading ' . $line[1]);
        file_put_contents($tmpFile, file_get_contents('http://localhost/~kiang/elections/api/candidates/s/' . urlencode($line[1])));
    }
    $c = file_get_contents($tmpFile);
    if (empty($c)) {
        error_log('downloading ' . $line[1]);
        file_put_contents($tmpFile, file_get_contents('http://localhost/~kiang/elections/api/candidates/s/' . urlencode($line[1])));
        $c = file_get_contents($tmpFile);
    }
    $json = json_decode($c, true);
    foreach ($keys AS $k => $v) {
        if (false === $keyFound && false !== strpos($line[0], $k)) {
            $keyFound = $v;
        }
    }
    $electionMatched = false;
    if (is_array($json)) {
        foreach ($json AS $election) {
            if (false === strpos($election['Election']['name'], '2016')) {
                continue;
            }
            if (false === $electionMatched && false !== strpos($election['Election']['name'], $keyFound)) {
                $electionMatched = $election;
            }
        }
    }
    if (is_array($electionMatched)) {
        fputcsv($oFh, array_merge(array($electionMatched['Election']['id'], $electionMatched['Election']['name'], $electionMatched['Candidate']['id']), $line));
    }
}
