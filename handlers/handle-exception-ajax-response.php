<?php

header('Content-type: application/json');

$response = new stdClass();
$response->wasSuccessful = false;
$response->message = get_class($exception) . ': ' . $exception->getMessage();

if (CBSitePreferences::debug()) {
    $response->stackTrace = Colby::exceptionStackTrace($exception);
}

echo json_encode($response);
