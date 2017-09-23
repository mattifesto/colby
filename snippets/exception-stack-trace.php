<?php

if (isset($_SERVER['SERVER_NAME'])) {
    echo $_SERVER['SERVER_NAME'], "\n";
}

if (isset($_SERVER['REQUEST_URI'])) {
    $parts = explode('?', $_SERVER['REQUEST_URI'], 2);
    echo implode("\n", $parts);
    echo "\n";
}

if (isset($_POST['ajax'])) {
    $ajaxModelAsJSON = cb_post_value('ajax');
    $ajaxModel = json_decode($ajaxModelAsJSON);
    $ajaxFunctionClassName = CBModel::value($ajaxModel, 'functionClassName', '(unset)');
    $ajaxFunctionName = CBModel::value($ajaxModel, 'functionName', '(unset)');
    echo "\nAjax:\n{$ajaxFunctionClassName}\n{$ajaxFunctionName}\n";
}

echo "\n\n";
echo CBConvert::throwableToStackTrace($exception);
