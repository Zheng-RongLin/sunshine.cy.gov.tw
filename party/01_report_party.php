<?php

class Crawler {

    public $totalFetched = false;
    public $totalPages = 10;
    public $tmpPath = '';

    public function main() {
        $this->tmpPath = dirname(__DIR__) . '/tmp/party/reports';
        if (!file_exists($this->tmpPath)) {
            mkdir($this->tmpPath, 0777, true);
        }
        $fp = fopen(__DIR__ . '/party_reports.csv', 'w');
        $results = array();
        for ($i = 1; $i <= $this->totalPages; $i ++) {
            foreach ($this->getData($i) as $result) {
                fputcsv($fp, $result);
            }
        }
    }

    public function getData($page) {
        $params = array(
            'xdUrl' => '/wSite/PoliticAccount/PAQuery.jsp',
            'doQuery' => '1',
            'accountType' => '政黨',
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

        $doc = new DOMDocument;
        @$doc->loadHTML($ret);

        $table_doms = $doc->getElementsByTagName('table');
        foreach ($table_doms as $table_dom) {
            if ($table_dom->getAttribute('summary') == '資料表格') {
                break;
            }
        }

        $results = array();
        if(!empty($table_dom)) {
          foreach ($table_dom->getElementsByTagName('tr') as $tr_dom) {
              $td_doms = $tr_dom->getElementsByTagName('td');
              $td_dom = $td_doms->item(0);
              if (!$td_dom) {
                  continue;
              }
              $result = array(
                'year' => $td_doms->item(0)->nodeValue,
                'name' => $td_doms->item(1)->nodeValue,
                'bank_account' => $td_doms->item(2)->nodeValue,
                'bank' => $td_doms->item(3)->nodeValue,
                'account_number' => $td_doms->item(4)->nodeValue,
                'bank_address' => $td_doms->item(5)->nodeValue,
                'report_url' => 'http://sunshine.cy.gov.tw' . $td_doms->item(6)->childNodes->item(1)->attributes->item(0)->nodeValue,
              );

              $results[] = $result;
          }
        }

        return $results;
    }

}

$c = new Crawler;
$c->main();
