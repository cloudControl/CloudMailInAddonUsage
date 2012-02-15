<?php
require 'error.php';
require 'config.php';

print_r($_POST);

if(!isset($_POST['from']) || !isset($_POST['to']) || !isset($_POST['plain'])) {
    print "missing data";
    exit;
}

$from = $_POST['from'];
$to = $_POST['to'];
$plain = $_POST['plain'];

$dsn = sprintf('mysql:host=%s;dbname=%s', $config['MYSQL_HOSTNAME'], $config['MYSQL_DATABASE']);
$pdo = new PDO($dsn, $config['MYSQL_USERNAME'], $config['MYSQL_PASSWORD']);
if (!$pdo) {
    print "No database connection";
    exit;
}

$insert = <<<SQL
INSERT INTO `mail`
    (date, from, to, plain)
VALUES
    (NOW(), :from, :to, :plain)
SQL;

$insertStmt = $pdo->prepare($insert);
$insertStmt->bindValue(':from', $from, PDO::PARAM_STR);
$insertStmt->bindValue(':to', $to, PDO::PARAM_STR);
$insertStmt->bindValue(':plain', $plain, PDO::PARAM_STR);
$execute = $insertStmt->execute();
var_dump($insert);
var_dump($execute);