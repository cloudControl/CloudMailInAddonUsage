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
require 'mailData.php';

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
 * build the mailData object
 * feel free to validate the fields as you need
 * in case of invalid data return null
 * @return \MailData 
 */
function buildMailData() {
    if(!isset($_POST['from']) 
        || !isset($_POST['to']) 
        || !isset($_POST['plain'])
        || !isset($_POST['subject'])) {
        return null;
    }
    
    $m = new MailData();
    $m->from = $_POST['from'];
    $m->to = $_POST['to'];
    $m->subject = $_POST['subject'];
    $m->plain = $_POST['plain'];
    $m->html = $_POST['html'];
    $m->x_remote_ip = $_POST['x_remote_ip'];
    return $m;
}

/**
 * store some e-mail data in the database
 * @param MailData $mailData
 * @return boolean
 */
function handleMail(MailData $mailData) {
    $config = new ConfigReader();
    $mysqlsConfig = $config->getAddonConfig('MYSQLS');

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
    $insertStmt->bindValue(':from', $mailData->from, PDO::PARAM_STR);
    $insertStmt->bindValue(':to', $mailData->to, PDO::PARAM_STR);
    $insertStmt->bindValue(':subject', $mailData->subject, PDO::PARAM_STR);
    $insertStmt->bindValue(':plain', $mailData->plain, PDO::PARAM_STR);
    $insertStmt->bindValue(':html', $mailData->html, PDO::PARAM_STR);
    $insertStmt->bindValue(':x_remote_ip', $mailData->x_remote_ip, PDO::PARAM_STR);
    $result = $insertStmt->execute();
    $insertStmt->closeCursor();
    $pdo->commit();
    return $result;
}

if (!verifySignature()) {
    myerror('verification error', 403);
}

$mailData = buildMailData();
if(!$mailData) {
    myerror('invalid or missing data', 400);
}

if(!handleMail($mailData)){
    myerror('database error', 500);
}
header("HTTP/1.0 200 OK");
