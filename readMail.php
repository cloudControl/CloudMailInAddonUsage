<?php
require 'config.php';

function myErrorHandler($errno, $errstr, $errfile, $errline) {
    print_r(array($errno, $errstr, $errfile, $errline));
}

set_error_handler("myErrorHandler");

function shutDownFunction() {
    print_r(error_get_last());
}

register_shutdown_function('shutdownFunction');



class MailDto {
    public $date;
    public $from;
    public $to;
    public $plain;
}

$dsn = sprintf('mysql:host=%s;dbname=%s', $config['MYSQL_HOSTNAME'], $config['MYSQL_DATABASE']);
$pdo = new PDO($dsn, $config['MYSQL_USERNAME'], $config['MYSQL_PASSWORD']);
if (!$pdo) {
    print "No database connection";
    exit;
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