<?php
$string = file_get_contents($_ENV['CRED_FILE'], false);
if ($string == false) {
    die('FATAL: Could not read credentials file');
}

# the file contains a JSON string, decode it and return an associative array
$creds = json_decode($string, true);

# use credentials to set the configuration for MySQL
$config = array(
    'MYSQL_HOSTNAME' => $creds['MYSQLS']['MYSQLS_HOSTNAME'],
    'MYSQL_DATABASE' => $creds['MYSQLS']['MYSQLS_DATABASE'],
    'MYSQL_USERNAME' => $creds['MYSQLS']['MYSQLS_USERNAME'],
    'MYSQL_PASSWORD' => $creds['MYSQLS']['MYSQLS_PASSWORD'],
    
    'CLOUDMAILIN_SECRET' => $creds['CLOUDMAILIN']['CLOUDMAILIN_SECRET'],
    'CLOUDMAILIN_USERNAME' => $creds['CLOUDMAILIN']['CLOUDMAILIN_USERNAME'],
    'CLOUDMAILIN_PASSWORD' => $creds['CLOUDMAILIN']['CLOUDMAILIN_PASSWORD'],
    'CLOUDMAILIN_FORWARD_ADDRESS' => $creds['CLOUDMAILIN']['CLOUDMAILIN_FORWARD_ADDRESS']
);