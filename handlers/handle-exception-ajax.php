<?php

header('Content-type: application/json');

$response = new stdClass();
$response->wasSuccessful = false;
$response->message = get_class($exception) . ': ' . $exception->getMessage();
$response->stackTrace = Colby::exceptionStackTrace($exception);
$response->stackTraceHTML = ColbyConvert::textToHTML($response->stackTrace);

echo json_encode($response);
