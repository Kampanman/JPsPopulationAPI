<?php

class Services
{

  public static function getHelpInfo($decoded_data, $username)
  {
    // このオブジェクト配列の中に本システムで使えるケースとコマンドを入れていく
    $res = array();
    $CURLSTART = 'curl -X POST -H "Content-Type: application/json" -d ';
    $CURLJSONHEAD = '"{\"type\" : \"curl\", ';
    $codehead = $CURLSTART . $CURLJSONHEAD;
    $CURLJSONEND = '}" ';
    $REQUESTFOR = "https://" . $username . ".sakura.ne.jp/JPsPopulationAPI/get-api.php";
    $codetail = $CURLJSONEND . $REQUESTFOR;

    $cc_objects = array(
      array(
        'case' => "・都道府県と政令市の一覧を取得したい",
        'code' => $codehead . '\"request\" : \"PandS\"' . $codetail
      ),
      array(
        'case' => "・指定した条件に応じた人口データを取得したい（全国を指定する場合）",
        'code' => $codehead . '\"request\" : \"Population\", \"prefecture\" : \"全国\"' . $codetail
      ),
      array(
        'case' => "・指定した条件に応じた人口データを取得したい（都道府県・政令市を指定する場合）",
        'code' => $codehead . '\"request\" : \"Population\", \"prefecture\" : \"神奈川県\", \"state\" : \"横浜市\"' . $codetail
      ),
      array(
        'case' => "・指定した条件に応じた人口データを取得したい（西暦を指定する場合）",
        'code' => $codehead . '\"request\" : \"Population\", \"year\" : 2020' . $codetail
      ),
      array(
        'case' => "・指定した都道府県または政令市の、人口各分類と、各５年前数値との差分を取得したい",
        'code' => $codehead . '\"request\" : \"PBeforeAfter\", \"prefecture\" : \"神奈川県\", \"state\" : \"横浜市\"' . $codetail
      ),
      array(
        'case' => "・指定した条件に応じた産業人口データを取得したい（全国を指定する場合）",
        'code' => $codehead . '\"request\" : \"IndustPopulation\", \"prefecture\" : \"全国\"' . $codetail
      ),
      array(
        'case' => "・指定した条件に応じた産業人口データを取得したい（都道府県・政令市を指定する場合）",
        'code' => $codehead . '\"request\" : \"IndustPopulation\", \"prefecture\" : \"神奈川県\", \"state\" : \"横浜市\"' . $codetail
      ),
      array(
        'case' => "・指定した条件に応じた産業人口データを取得したい（西暦を指定する場合）",
        'code' => $codehead . '\"request\" : \"IndustPopulation\", \"year\" : 2020' . $codetail
      ),
      array(
        'case' => "・指定した条件に応じた通勤人口データを取得したい（全国を指定する場合）",
        'code' => $codehead . '\"request\" : \"CommuterPopulation\", \"prefecture\" : \"全国\"' . $codetail
      ),
      array(
        'case' => "・指定した条件に応じた通勤人口データを取得したい（都道府県・政令市を指定する場合）",
        'code' => $codehead . '\"request\" : \"CommuterPopulation\", \"prefecture\" : \"神奈川県\", \"state\" : \"横浜市\"' . $codetail
      ),
      array(
        'case' => "・指定した条件に応じた通勤人口データを取得したい（西暦を指定する場合）",
        'code' => $codehead . '\"request\" : \"CommuterPopulation\", \"year\" : 2020' . $codetail
      ),
      array(
        'case' => "・当システムに関係するテーブルと内部カラムの情報を取得したい",
        'code' => $codehead . '\"request\" : \"TablesInfo\"' . $codetail
      ),
    );
    foreach ($cc_objects as $i => $row) {
      $res[$i] = $row;
    }

    return $res;
  }

  public static function switchGetSQL($decoded_data, $connection)
  {
    $sql_res = "";

    if ($decoded_data->request == 'PandS') $sql_res = SQLGets::getPrefecturesAndStates($connection);
    if ($decoded_data->request == 'Population') $sql_res = SQLGets::getPopulationByRequest($connection, $decoded_data);
    if ($decoded_data->request == 'PBeforeAfter') $sql_res = SQLGets::getPopulationBeforeAfterByRequest($connection, $decoded_data);
    if ($decoded_data->request == 'IndustPopulation') $sql_res = SQLGets::getIndustPopulationByRequest($connection, $decoded_data);
    if ($decoded_data->request == 'CommuterPopulation') $sql_res = SQLGets::getCommuterPopulationByRequest($connection, $decoded_data);
    if ($decoded_data->request == 'TablesInfo') $sql_res = SQLGets::getTablesInfo($connection);
    $object['ths'] = array_keys($sql_res[0]);
    $object['tds'] = $sql_res;

    return $object;
  }

  public static function generateCSVRows($decoded_data, $object)
  {
    $res = "";
    if ($decoded_data->type == null || $decoded_data->type != 'curl') {
      $object["message"] = "通信に成功しました！";
      $res = json_encode($object, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); // 日本語と改行も適用されるようにUNICODEを指定
    } else {
      if ($decoded_data->help == true) {
        $help_rows = array();
        foreach ($object as $h => $lane) {
          $help_rows[$h] = $lane['case'] . "\n => " . $lane['code'];
        };
        $res = implode("\n", $help_rows) . "\n";
      } else {
        if (count($object['ths']) < 1) {
          $res = "Sorry..., No records were found that match your request.";
        } else {
          $th_row = implode(",", $object['ths']);
          $td_rows = array();
          foreach ($object['tds'] as $i => $row) {
            $td_rows[$i] = implode(",", $row);
          }
          $res = $th_row . "\n" . implode("\n", $td_rows) . "\n";
        }
      }
    }

    return $res;
  }
}
