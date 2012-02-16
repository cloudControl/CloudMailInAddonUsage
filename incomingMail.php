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
    $httpError = array(
        400 => "Bad Request",
        403 => "Forbidden",
        500 => "Internal Server Error"
    );
    header(sprintf("HTTP/1.0 %d %s", $code, $httpError[$code]));
    echo($msg);
    exit;
}

/**
 * verify the e-mail by the requests signature
 * @return boolean 
 */
function verifySignature(){
    $config = new ConfigReader();
    $cloudmailinConfig = $config->getAddonConfig('CLOUDMAILIN');
    
    $provided = $_POST['signature'];
    $params = $_POST;
    unset($params['signature']);
    ksort($params);
    $str = implode('', array_values($params));
    $signature = md5($str . $cloudmailinConfig['CLOUDMAILIN_SECRET']);
    return $provided == $signature;
}

/**
 * store some e-mail data in the database
 * @return boolean
 */
function storeMail() {
    $config = new ConfigReader();
    $mysqlsConfig = $config->getAddonConfig('MYSQLS');

    $from = $_POST['from'];
    $to = $_POST['to'];
    $subject = $_POST['subject'];
    $plain = $_POST['plain'];
    $html = $_POST['html'];
    $x_remote_ip = $_POST['x_remote_ip'];

    $dsn = sprintf('mysql:host=%s;dbname=%s', $mysqlsConfig['MYSQLS_HOSTNAME'], $mysqlsConfig['MYSQLS_DATABASE']);
    $pdo = new PDO($dsn, $mysqlsConfig['MYSQLS_USERNAME'], $mysqlsConfig['MYSQLS_PASSWORD']);
    if (!$pdo) {
        return false;
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

if(!isset($_POST['from']) 
    || !isset($_POST['to']) 
    || !isset($_POST['plain'])
    || !isset($_POST['subject'])) {
    myerror("missing data", 400);
}
if (!verifySignature()) {
    myerror('verification error', 403);
}
if(!storeMail()){
    myerror('database error', 500);
}
header("HTTP/1.0 200 OK");
