<?php

class SQLGets
{

  public static function getPrefecturesAndStates($connection)
  {
    // 都道府県と政令市の一覧を取得;
    $sql = "SELECT DISTINCT `prefecture`, "
      . "CASE WHEN `state` REGEXP '(全国|都|道|府|県|区部)$' THEN '―' ELSE `state` END AS state "
      . "FROM `m_census_industpopulation` "
      . "WHERE `state` NOT REGEXP '[^@]市$'";
    $statement = $connection->prepare($sql);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    return $result;
  }

  public static function getPopulationByRequest($connection, $decoded_data)
  {
    $after_where = "";
    if ($decoded_data->prefecture != null) {
      $after_where = "WHERE `prefecture` = '" . $decoded_data->prefecture . "' ";
      if ($decoded_data->state != null) {
        $after_where .= "AND `state` = '" . $decoded_data->state . "'";
      }
      if ($decoded_data->year != null) {
        $after_where .= "AND `year` = '" . $decoded_data->year . "' ";
      }
    } else {
      if ($decoded_data->year != null) {
        $after_where = "WHERE `year` = '" . $decoded_data->year . "' ";
      }
    }
    // 指定した都道府県または政令市の人口データを取得（すべてのカラムを指定している）
    $sql = "SELECT `year`, `prefecture`, `state`, `p_total`, `p_male`, `p_female`, "
      . "`p_5years_ago`, `p_c_5ys_amount`, `p_c_5ys_rate`, `area`, `density`, `age_avr`, `age_center`, "
      . "`population_under15`, `population_15for64`, `population_over65`, `rate_under15`, `rate_15for64`, `rate_over65`, "
      . "`male_under15`, `male_15for64`, `male_over65`, `rate_male_under15`, `rate_male_15for64`, `rate_male_over65`, "
      . "`female_under15`, `female_15for64`, `female_over65`, `rate_female_under15`, `rate_female_15for64`, `rate_female_over65`, `gender_ratio`, "
      . "`japanese_population`, `forigner_population` "
      . "FROM `m_census_statepopulation` " . $after_where;
    $statement = $connection->prepare($sql);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    return $result;
  }

  public static function getPopulationBeforeAfterByRequest($connection, $decoded_data)
  {
    $after_where = "";
    if ($decoded_data->prefecture == null) {
      $after_where = "WHERE `prefecture` = '全国' ";
    } else {
      $after_where = "WHERE `prefecture` = '" . $decoded_data->prefecture . "' ";
      if ($decoded_data->state != null) {
        $after_where .= "AND `state` = '" . $decoded_data->state . "'";
      }
    }
    // 指定した都道府県または政令市の、人口各分類と、各５年前数値との差分を取得
    $sql = "SELECT `year`, `prefecture`, `state`, `p_total`,"
      . "CASE WHEN year = 2000 THEN NULL ELSE (@pu15_diff := population_under15 - @pu15) END pu15_diff,"
      . "(@pu15 := population_under15) pu15,"
      . "CASE WHEN year = 2000 THEN NULL ELSE (@p15_64_diff := population_15for64 - @p15_64) END p15_64_diff,"
      . "(@p15_64 := population_15for64) p15_64,"
      . "CASE WHEN year = 2000 THEN NULL ELSE (@po65_diff := population_over65 - @po65) END po65_diff,"
      . "(@po65 := population_over65) po65,"
      . "`rate_under15`, `rate_15for64`, `rate_over65`, "
      . "CASE WHEN year = 2000 THEN NULL ELSE (@jp_diff := japanese_population - @jp) END jp_diff,"
      . "(@jp := japanese_population) jp, "
      . "CASE WHEN year = 2000 THEN NULL ELSE (@frp_diff := forigner_population - @frp) END frp_diff,"
      . "(@frp := forigner_population) frp "
      . "FROM (SELECT `year`, `prefecture`, `state`, `p_total`, "
      . "`population_under15`, `population_15for64`, `population_over65`, "
      . "`rate_under15`, `rate_15for64`, `rate_over65`, "
      . "`japanese_population`, `forigner_population` "
      . "FROM `m_census_statepopulation`) t,"
      . "(SELECT @pu15 := 0) pu15, (SELECT @pu15_diff := 0) pu15_diff,"
      . "(SELECT @p15_64 := 0) p15_64, (SELECT @p15_64_diff := 0) p15_64_diff,"
      . "(SELECT @po65 := 0) po65, (SELECT @po65_diff := 0) po65_diff,"
      . "(SELECT @jp := 0) jp, (SELECT @jp_diff := 0) jp_diff,"
      . "(SELECT @frp := 0) frp, (SELECT @frp_diff := 0) frp_diff "
      . $after_where . "ORDER BY year";
    $statement = $connection->prepare($sql);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    return $result;
  }

  public static function getIndustPopulationByRequest($connection, $decoded_data)
  {
    $after_where = "";
    if ($decoded_data->prefecture != null) {
      $after_where = "WHERE `prefecture` = '" . $decoded_data->prefecture . "' ";
      if ($decoded_data->state != null) {
        $after_where .= "AND `state` = '" . $decoded_data->state . "'";
      }
      if ($decoded_data->year != null) {
        $after_where .= "AND `year` = '" . $decoded_data->year . "' ";
      }
    } else {
      if ($decoded_data->year != null) {
        $after_where = "WHERE `year` = '" . $decoded_data->year . "' ";
      }
    }
    // 指定した都道府県または政令市の産業人口データを取得（カラムはすべて表示）
    $sql = "SELECT `year`, `prefecture`, `state`, `employed_population`, "
      . "`nouringyou`, `gyogyou`, `kousaisekigyou`, `kensetsugyou`, `seizougyou`, `lifelines`, "
      . "`it`, `transport`, `oroshikouri`, `kinyuuhoken`, `fudosan`, `gakken`, "
      . "`inneat`, `lifesapport`, `learning`, `medical`, `fukugoservice`, `otherservice`, `publicity`, `other` "
      . "FROM `m_census_industpopulation` " . $after_where;
    $statement = $connection->prepare($sql);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    return $result;
  }

  public static function getCommuterPopulationByRequest($connection, $decoded_data)
  {
    $after_where = "";
    if ($decoded_data->prefecture != null) {
      $after_where = "WHERE stp.prefecture = '" . $decoded_data->prefecture . "' ";
      if ($decoded_data->state != null) {
        $after_where .= "AND stp.state = '" . $decoded_data->state . "'";
      }
      if ($decoded_data->year != null) {
        $after_where .= "AND stp.year = '" . $decoded_data->year . "' ";
      }
    } else {
      if ($decoded_data->year != null) {
        $after_where = "WHERE stp.year = '" . $decoded_data->year . "' ";
      }
    }
    // 指定した都道府県または政令市の産業人口データを取得（カラムはすべて表示）
    $sql = "SELECT "
      . "stp.year, stp.prefecture, stp.state, p_total, japanese_population, forigner_population,"
      . "commuter_worker, commuter_student, daytime_population, dp_rate, p_outflow, p_inflow "
      . "FROM m_census_statepopulation stp JOIN m_census_inoutflow iof "
      . "ON stp.year = iof.year AND stp.prefecture = iof.prefecture AND stp.state = iof.state "
      . $after_where;
    var_dump($sql);
    $statement = $connection->prepare($sql);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    return $result;
  }

  public static function getTablesInfo($connection)
  {
    // 当システムに関係するテーブルと内部カラムの情報を取得;
    $sql = "(select TABLE_NAME, '都道府県・政令市人口' AS TABLE_COMMENT, COLUMN_NAME, COLUMN_COMMENT from INFORMATION_SCHEMA.COLUMNS "
      . " where TABLE_SCHEMA = 'empower-util_mydb' and TABLE_NAME = 'm_census_statepopulation') UNION "
      . "(select TABLE_NAME, '都道府県・政令市産業人口' AS TABLE_COMMENT, COLUMN_NAME, COLUMN_COMMENT from INFORMATION_SCHEMA.COLUMNS "
      . " where TABLE_SCHEMA = 'empower-util_mydb' and TABLE_NAME = 'm_census_industpopulation') UNION "
      . "(select TABLE_NAME, '都道府県・政令市流出入人口' AS TABLE_COMMENT, COLUMN_NAME, COLUMN_COMMENT from INFORMATION_SCHEMA.COLUMNS"
      . " where TABLE_SCHEMA = 'empower-util_mydb' and TABLE_NAME = 'm_census_inoutflow')";
    $statement = $connection->prepare($sql);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    return $result;
  }
}
