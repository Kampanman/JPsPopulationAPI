<?php

/**
 * プロパティ諸設定
 */
$contents_name = "JP's Populastion API";

/*
$dsn = 'localhost';
$dbname = 'crud';
$username = 'root';
$password = '';
*/
$dsn = 'mysql57.empower-util.sakura.ne.jp';
$dbname = 'empower-util_mydb';
$username = 'empower-util';
$password = 'i4237137ns';

$connection = new PDO('mysql:host=' . $dsn . ';dbname=' . $dbname, $username, $password);
