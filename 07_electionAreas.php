<?php

$path = __DIR__ . '/tmp/areas';
if (!file_exists($path)) {
    mkdir($path, 0777, true);
}

$fh = fopen(__DIR__ . '/report2elections.csv', 'r');
/*
  (
  [0] => election_id
  [1] => election
  [2] => candidate_id
  [3] => 選舉類型
  [4] => 擬參選人
  [5] => 個人捐贈收入
  [6] => 營利事業捐贈收入
  [7] => 政黨捐贈收入
  [8] => 人民團體捐贈收入
  [9] => 匿名捐贈收入
  [10] => 其他收入
  [11] => 人事費用支出
  [12] => 宣傳支出
  [13] => 租用宣傳車輛支出
  [14] => 租用競選辦事處支出
  [15] => 集會支出
  [16] => 交通旅運支出
  [17] => 雜支支出
  [18] => 返還捐贈支出
  [19] => 繳庫支出
  [20] => 公共關係費用支出
  )
 */
fgetcsv($fh, 2048);
$done = array();
while ($line = fgetcsv($fh, 2048)) {
    if (!isset($done[$line[0]])) {
        $geoJson = new stdClass();
        $geoJson->type = 'FeatureCollection';
        $geoJson->features = array();
        $candidates = array();
        $records = json_decode(file_get_contents('http://localhost/~kiang/elections/api/elections/candidates/' . $line[0]), true);
        foreach ($records AS $record) {
            $candidates[] = array(
                'id' => $record['Candidate']['id'],
                'stage' => $record['Candidate']['stage'],
                'name' => $record['Candidate']['name'],
                'party' => $record['Candidate']['party'],
            );
        }
        $areas = json_decode(file_get_contents('http://localhost/~kiang/elections/areas/election/' . $line[0]), true);
        $areaCount = 0;
        foreach ($areas AS $area) {
            $f = new stdClass();
            $f->type = 'Feature';
            $f->geometry = json_decode($area['Area']['polygons']);
            if (!empty($f->geometry)) {
                ++$areaCount;
            }
            unset($area['Area']['polygons']);
            $area['Area']['election_id'] = $line[0];
            $area['Area']['election'] = $line[1];
            $area['Area']['candidates'] = $candidates;
            $f->properties = $area['Area'];
            $geoJson->features[] = $f;
        }
        if ($areaCount > 0) {
            file_put_contents($path . '/' . $line[0] . '.json', json_encode($geoJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
        $done[$line[0]] = true;
    }
}