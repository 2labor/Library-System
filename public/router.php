<?php
/*
 * Router for handling incoming HTTP requests
 *
 * File: public/router.php
 */
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

if (php_sapi_name() === 'cli-server') {
    $file = __DIR__ . $uri;
    if (is_file($file)) {
        return false;
    }
}

require_once __DIR__ . '/index.php';
