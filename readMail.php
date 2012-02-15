<?php
require 'config.php';

class MailDto {
    public $date;
    public $from;
    public $to;
    public $plain;
}

print_r(new MailDto());
exit;

$dsn = sprintf('mysql:host=%s;dbname=%s', $config['MYSQL_HOSTNAME'], $config['MYSQL_DATABASE']);
$pdo = new PDO($dsn, $config['MYSQL_USERNAME'], $config['MYSQL_PASSWORD']);
if (!$pdo) {
    throw new Exception("No database connection", 1);
}

$select = <<<SQL
SELECT date, from, to, plain FROM `mail`
SQL;

$selectStmt = $pdo->prepare($select);
if ($selectStmt->execute()) {
    $result = $selectStmt->fetchAll(PDO::FETCH_CLASS, 'MailDto');
    $selectStmt->closeCursor();
}
$pdo->commit();

print_r($result);
exit;
?>