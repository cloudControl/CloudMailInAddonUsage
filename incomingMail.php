<?php
require 'config.php';

header("Content-type: text/plain");

function myerror($msg) {
    header("HTTP/1.0 403 OK");
    echo($msg);
    exit;
}

function verifySignature(){
    global $config;

    $provided = $_POST['signature'];
    $params = $_POST;
    unset($params['signature']);
    ksort($params);
    $str = implode('', array_values($params));
    $signature = md5($str . $config['CLOUDMAILIN_SECRET']);
    
    return $provided == $signature;
}

function storeMail() {
    global $config;
    
    $from = $_POST['from'];
    $to = $_POST['to'];
    $subject = $_POST['subject'];
    $plain = $_POST['plain'];
    $html = $_POST['html'];
    $x_remote_ip = $_POST['x_remote_ip'];

    $dsn = sprintf('mysql:host=%s;dbname=%s', $config['MYSQL_HOSTNAME'], $config['MYSQL_DATABASE']);
    $pdo = new PDO($dsn, $config['MYSQL_USERNAME'], $config['MYSQL_PASSWORD']);
    if (!$pdo) {
        myerror("No database connection");
    }

    $insert = <<<SQL
INSERT INTO `mail`
    (`date`, `from`, `to`, `subject`, `plain`, `html`, `x_remote_ip`)
VALUES
    (NOW(), :from, :to, :subject, :plain, :html, :x_remote_ip)
SQL;
    $insertStmt = $pdo->prepare($insert);
    $insertStmt->bindValue(':from', $from, PDO::PARAM_STR);
    $insertStmt->bindValue(':to', $to, PDO::PARAM_STR);
    $insertStmt->bindValue(':subject', $subject, PDO::PARAM_STR);
    $insertStmt->bindValue(':plain', $plain, PDO::PARAM_STR);
    $insertStmt->bindValue(':html', $html, PDO::PARAM_STR);
    $insertStmt->bindValue(':x_remote_ip', $x_remote_ip, PDO::PARAM_STR);
    return $insertStmt->execute();
}

if(!isset($_POST['from']) || !isset($_POST['to']) || !isset($_POST['plain'])) {
    myerror("missing data");
}

if (!verifySignature()) {
    myerror('verification error');
}

if(storeMail()){
    header("HTTP/1.0 200 OK");
} else {
    myerror('database error');
}
