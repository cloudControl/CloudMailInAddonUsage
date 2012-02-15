<?php
/**
 * the script reacts on a forwarded e-mail message from cloudmailin
 * 
 * the e-mail data are stored in the requests post params
 * the steps are:
 *  - verify the e-mail by the requests signature
 *  - store some e-mail data in the database
 */

require 'config.php';

// the content type to answer the cloudmailin request
header("Content-type: text/plain");

/**
 * sets the http response code and response message
 * @param string $msg
 * @param int $code 
 */
function myerror($msg, $code = 403) {
    header(sprintf("HTTP/1.0 %d OK", $code));
    echo($msg);
    exit;
}

/**
 * verify the e-mail by the requests signature
 * @global array $config
 * @return boolean 
 */
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

/**
 * store some e-mail data in the database
 * @global array $config
 * @return boolean
 */
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

    $pdo->beginTransaction();
    $insertStmt = $pdo->prepare($insert);
    $insertStmt->bindValue(':from', $from, PDO::PARAM_STR);
    $insertStmt->bindValue(':to', $to, PDO::PARAM_STR);
    $insertStmt->bindValue(':subject', $subject, PDO::PARAM_STR);
    $insertStmt->bindValue(':plain', $plain, PDO::PARAM_STR);
    $insertStmt->bindValue(':html', $html, PDO::PARAM_STR);
    $insertStmt->bindValue(':x_remote_ip', $x_remote_ip, PDO::PARAM_STR);
    $result = $insertStmt->execute();
    $insertStmt->closeCursor();
    $pdo->commit();
    return $result;
}

if(!isset($_POST['from']) || !isset($_POST['to']) || !isset($_POST['plain'])) {
    myerror("missing data");
}
if (!verifySignature()) {
    myerror('verification error');
}
if(!storeMail()){
    myerror('database error');
}
header("HTTP/1.0 200 OK");
