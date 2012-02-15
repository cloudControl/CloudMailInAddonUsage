<?php
require 'config.php';

$from = $_POST['from'];
$to = $_POST['to'];
$plain_text = $_POST['plain'];

print_r($_POST);

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
$insertStmt->bindValue(':plain', $plain_text, PDO::PARAM_STR);
if ($insertStmt->execute()) {
    $insertStmt->closeCursor();
}
$pdo->commit();
exit;
?>