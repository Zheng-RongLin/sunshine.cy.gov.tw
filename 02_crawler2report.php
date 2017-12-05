<?php

class Crawler {

    public $totalFetched = false;
    public $totalPages = 10;
    public $tmpPath = '';
    public $pool = array();

    public function main() {
        $this->tmpPath = __DIR__ . '/tmp/reports';
        if (!file_exists($this->tmpPath)) {
            mkdir($this->tmpPath, 0777, true);
        }
        $fp = fopen(__DIR__ . '/reports.csv', 'w');
        $results = array();
        for ($i = 1; $i <= $this->totalPages; $i ++) {
          $results = $this->getData($i);
            foreach ($results as $result) {
              if(!isset($this->pool[$result->file])) {
                $this->pool[$result->file] = true;
                fputcsv($fp, array(
                    $result->name,
                    $result->account_name,
                    $result->file,
                    $result->file1,
                    $result->file2,
                    $result->file3,
                    $result->file4,
                    $result->file5,
                ));
              }
            }
        }
    }

    public function getData($page) {
      error_log("page {$page}");
        $params = array(
            'xdUrl' => '/wSite/PoliticAccount/PAQuery.jsp',
            'doQuery' => '1',
            'accountType' => '擬參選人',
            'accountName' => '',
            'keyword' => '',
            'lv2' => '',
            'lv4' => '',
            'lv3' => '',
            'electionId' => '',
            'buttonType' => '3',
            'politicYear' => '',
            'orderFlag' => '',
            'page' => $page,
        );
        $terms = array();
        foreach ($params as $key => $value) {
            $terms[] = urlencode($key) . '=' . urlencode($value);
        }
        $url = 'http://sunshine.cy.gov.tw/GipOpenWeb/wSite/sp';
        $url .= '?' . implode('&', $terms);
        $cachedFile = $this->tmpPath . '/' . md5($url);
        if (!file_exists($cachedFile)) {
            $curl = curl_init($url);
            error_log("{$url} -> {$cachedFile}");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            file_put_contents($cachedFile, curl_exec($curl));
        }

        $ret = file_get_contents($cachedFile);

        if (false === $this->totalFetched) {
            $pos = strpos($ret, '<div class="page">');
            $pos = strpos($ret, '<strong>', $pos) + 8;
            $this->totalPages = substr($ret, $pos, strpos($ret, '</', $pos) - $pos);
            $this->totalFetched = true;
        }

        $results = array();

        $pos = strpos($ret, '<td class="number">');
        $posEnd = strpos($ret, '</table>', $pos);
        $key = '/GipOpenWeb/wSite/public/';

        $lines = explode('<tr>', substr($ret, $pos, $posEnd - $pos));
        foreach($lines AS $line) {
          $cols = explode('</td>', $line);

          $result = new StdClass;
          $result->name = trim(strip_tags($cols[0]));
          $result->account_name = trim(strip_tags($cols[1]));
          $pos = strpos($cols[2], $key);
          $posEnd = strpos($cols[2], '"', $pos);
          $result->file = 'http://sunshine.cy.gov.tw' . substr($cols[2], $pos, $posEnd - $pos);

          if(isset($cols[3])) {
            $pos = strpos($cols[3], $key);
            $posEnd = strpos($cols[3], '"', $pos);
            $result->file1 = 'http://sunshine.cy.gov.tw' . substr($cols[3], $pos, $posEnd - $pos);
          } else {
            $result->file1 = '';
          }
          if(isset($cols[4])) {
            $pos = strpos($cols[4], $key);
            $posEnd = strpos($cols[4], '"', $pos);
            $result->file2 = 'http://sunshine.cy.gov.tw' . substr($cols[4], $pos, $posEnd - $pos);
          } else {
            $result->file2 = '';
          }
          if(isset($cols[5])) {
            $pos = strpos($cols[5], $key);
            $posEnd = strpos($cols[5], '"', $pos);
            $result->file3 = 'http://sunshine.cy.gov.tw' . substr($cols[5], $pos, $posEnd - $pos);
          } else {
            $result->file3 = '';
          }
          if(isset($cols[6])) {
            $pos = strpos($cols[6], $key);
            $posEnd = strpos($cols[6], '"', $pos);
            $result->file4 = 'http://sunshine.cy.gov.tw' . substr($cols[6], $pos, $posEnd - $pos);
          } else {
            $result->file4 = '';
          }
          if(isset($cols[7])) {
            $pos = strpos($cols[7], $key);
            $posEnd = strpos($cols[7], '"', $pos);
            $result->file5 = 'http://sunshine.cy.gov.tw' . substr($cols[7], $pos, $posEnd - $pos);
          } else {
            $result->file5 = '';
          }
          $results[] = $result;
        }

        return $results;
    }

}

$c = new Crawler;
$c->main();
