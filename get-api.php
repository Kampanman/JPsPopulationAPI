<?php
// コマンドプロンプトからのサンプルコード
// curl -X POST -H "Content-Type: application/json" -d "{\"type\" : \"curl\", \"help\" : true}" https://empower-util.sakura.ne.jp/JPsPopulationAPI/get-api.php
$res = "";

// PDO情報を読み込む
include("./properties.php");
// クラスディレクトリから必要なクラスファイルを読み込む
include("./class/SQLGets.php");
include("./class/Services.php");

// POSTデータを受け取る
$post_data = file_get_contents("php://input");
$decoded_data = json_decode($post_data); // JSONデータを連想配列に変換

// レスポンスを返す
if ($decoded_data == null) {
  $res = "Sorry... POST failed.";
} else {
  // リクエスト内のJSONでhelpのプロパティが有効か否かで処理を分ける
  if ($decoded_data->help == true) {
    $data = Services::getHelpInfo($decoded_data, $username);
  } else {
    $data = Services::switchGetSQL($decoded_data, $connection);
  }
  $res = Services::generateCSVRows($decoded_data, $data);
}

echo $res;
