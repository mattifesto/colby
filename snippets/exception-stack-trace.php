<?php

if (isset($_SERVER['SERVER_NAME']))
{
    echo '   Server name: ', $_SERVER['SERVER_NAME'], "\n";
}

if (isset($_SERVER['REQUEST_URI']))
{
    echo '   Request URI: ', $_SERVER['REQUEST_URI'], "\n";
}

if (isset($_SERVER['QUERY_STRING']))
{
    echo '  Query String: ', $_SERVER['QUERY_STRING'], "\n\n";
}

echo 'Exception type: ', get_class($exception), "\n";
echo '       Message: ', $exception->getMessage(), "\n\n";

echo '## ', $exception->getFile(), '(', $exception->getLine(), ')', "\n";
echo $exception->getTraceAsString();
