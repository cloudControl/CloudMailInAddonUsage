<?php

$html = <<<html
 <html>
    <head></head>
    <body>
        <h1>Error</h1>
        <pre style="disply:block;background-color:silver;border:1px groove;padding:5px;">
            %s
        </pre>
    </body>
</html>
html;

function myErrorHandler($errno, $errstr, $errfile, $errline) {
    global $html;
    echo sprintf($html, print_r(array($errno, $errstr, $errfile, $errline), true));
    die();
}
set_error_handler("myErrorHandler");

function shutDownFunction() {
    global $html;
    echo sprintf($html, print_r(error_get_last(), true));
    die();
}
register_shutdown_function('shutdownFunction');
