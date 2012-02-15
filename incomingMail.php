<?php
require 'error.php';
require 'config.php';

header("Content-type: text/plain");

function myerror($msg) {
    header("HTTP/1.0 403 OK");
    echo($msg);
    exit;
}

if(!isset($_POST['from']) || !isset($_POST['to']) || !isset($_POST['plain'])) {
    myerror("missing data");
}

$from = $_POST['from'];
$to = $_POST['to'];

$dsn = sprintf('mysql:host=%s;dbname=%s', $config['MYSQL_HOSTNAME'], $config['MYSQL_DATABASE']);
$pdo = new PDO($dsn, $config['MYSQL_USERNAME'], $config['MYSQL_PASSWORD']);
if (!$pdo) {
    myerror("No database connection");
}

$insert = <<<SQL
INSERT INTO `mail`
    (`date`, `from`, `to`, `post`)
VALUES
    (NOW(), :from, :to, :post)
SQL;

$insertStmt = $pdo->prepare($insert);
$insertStmt->bindValue(':from', $from, PDO::PARAM_STR);
$insertStmt->bindValue(':to', $to, PDO::PARAM_STR);
$insertStmt->bindValue(':post', serialize($_POST), PDO::PARAM_STR);
$execute = $insertStmt->execute();

if($execute){
    header("HTTP/1.0 200 OK");
} else {
    myerror('database error');
}
