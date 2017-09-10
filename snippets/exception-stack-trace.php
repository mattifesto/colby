<?php

if (isset($_SERVER['SERVER_NAME'])) {
    echo $_SERVER['SERVER_NAME'], "\n";
}

if (isset($_SERVER['REQUEST_URI'])) {
    $parts = explode('?', $_SERVER['REQUEST_URI'], 2);
    echo implode("\n", $parts);
}

echo "\n\n\n";
echo CBConvert::throwableToStackTrace($exception);
