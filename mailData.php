<?php

/**
 * class to store the mail data 
 */
class MailData {
    
    public $date;
    public $from;
    public $to;
    public $subject;
    public $plain;
    public $html;
    public $x_remote_ip;
    
    public function __set($name, $value) {
        if(array_key_exists($name, get_class_vars(get_class($this)))){
            $this->{$name} = $value;
        } else {
            throw new Exception(sprintf('%s is not a property of MailData', $name));
        }
    }
}
