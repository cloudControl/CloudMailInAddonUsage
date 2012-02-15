<?php
function myErrorHandler($errno, $errstr, $errfile, $errline) {
    print_r(array($errno, $errstr, $errfile, $errline));
}
set_error_handler("myErrorHandler");

function shutDownFunction() {
    print_r(error_get_last());
}
register_shutdown_function('shutdownFunction');