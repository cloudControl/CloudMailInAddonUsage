<?php

class ConfigReader {
    
    private static $_config;
    
    public function __construct() {
        if (!self::$_config) {
            $this->_readConfig();
        }
    }
    
    public function getAddonConfig($addonName) {
        return self::$_config[$addonName];
    }
    
    private function _readConfig() {
        $string = file_get_contents($_ENV['CRED_FILE'], false);
        if ($string == false) {
            die('FATAL: Could not read credentials file');
        }
        self::$_config = json_decode($string, true);
    }
}