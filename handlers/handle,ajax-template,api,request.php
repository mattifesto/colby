<?php

include_once CBSystemDirectory . '/classes/CBAjaxResponse.php';


$response = new CBAjaxResponse();

$response->message          = 'Hello, world!';
$response->wasSuccessful    = true;
$response->send();
