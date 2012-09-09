<?php

header('Content-type: application/json');

$response = new stdClass();
$response->wasSuccessful = false;
$response->message = 'You must log in to use this feature.';

echo json_encode($response);
