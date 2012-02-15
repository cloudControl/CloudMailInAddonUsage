<?php
/**
 * the script reads e-mail messages from database 
 */
require 'config.php';

class MailData {
    public $date;
    public $from;
    public $to;
    public $subject;
    public $plain;
    public $html;
    public $x_remote_ip;
}

$dsn = sprintf('mysql:host=%s;dbname=%s', $config['MYSQL_HOSTNAME'], $config['MYSQL_DATABASE']);
$pdo = new PDO($dsn, $config['MYSQL_USERNAME'], $config['MYSQL_PASSWORD']);
if (!$pdo) {
    print "No database connection";
    exit;
}

$result = array();
$pdo->beginTransaction();
$selectStmt = $pdo->prepare('SELECT `date`, `from`, `to`, `subject`, `plain`, `html`, `x_remote_ip` FROM `mail`');
if ($selectStmt->execute()) {
    $result = $selectStmt->fetchAll(PDO::FETCH_CLASS, 'MailData');
    $selectStmt->closeCursor();
}
$pdo->commit();
?>
<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head></head>
    <body>
        <h1>Mail List</h1>
        <pre style="disply:block;background-color:silver;border:1px groove;padding:5px;">
            <?php
print_r($result);
            ?>
        </pre>
    </body>
</html>