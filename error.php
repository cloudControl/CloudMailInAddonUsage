<?php
function myErrorHandler($errno, $errstr, $errfile, $errline) {
    print_r(array($errno, $errstr, $errfile, $errline));
    die();
}
set_error_handler("myErrorHandler");

function shutDownFunction() {
    print_r(error_get_last());
    die();
}
register_shutdown_function('shutdownFunction');