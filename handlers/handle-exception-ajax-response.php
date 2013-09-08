<?php

header('Content-type: application/json');

$response = new stdClass();
$response->wasSuccessful = false;
$response->message = get_class($exception) . ': ' . $exception->getMessage();
$response->stackTrace = Colby::exceptionStackTrace($exception);

/**
 * Exception handlers should avoid calling external functions. The code below
 * is and should remain an exact duplicate of what `ColbyConvert::textToHTML`
 * does.
 */

$response->stackTraceHTML = htmlspecialchars($response->stackTrace, ENT_QUOTES);

echo json_encode($response);
