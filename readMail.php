<?php
require 'error.php';
require 'config.php';

class MailDto {
    public $date;
    public $from;
    public $to;
    public $post;
}

$dsn = sprintf('mysql:host=%s;dbname=%s', $config['MYSQL_HOSTNAME'], $config['MYSQL_DATABASE']);
$pdo = new PDO($dsn, $config['MYSQL_USERNAME'], $config['MYSQL_PASSWORD']);
if (!$pdo) {
    print "No database connection";
    exit;
}

$select = <<<SQL
SELECT `date`, `from`, `to`, `plain` FROM `mail`
SQL;

$result = array();
$pdo->beginTransaction();
$selectStmt = $pdo->prepare($select);
if ($selectStmt->execute()) {
    $result = $selectStmt->fetchAll(PDO::FETCH_CLASS, 'MailDto');
    $selectStmt->closeCursor();
}
$pdo->commit();

if(count($result)){
    array_walk($result, function($mail){
        $mail->post = unserialize($mail->post);
    });
}
?>
<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head></head>
    <body>
        <h1>Mails</h1>
        <pre style="disply:block;background-color:silver;border:1px groove;padding:5px;">
            <?php
                print_r($result);
            ?>
        </pre>
    </body>
</html>