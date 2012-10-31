<?php

Colby::useAjax();

ColbyAjax::requireVerifiedUser();

ColbyAjax::begin();

$response = new stdClass();
$response->wasSuccessful = false;
$response->message = 'incomplete';

$response->wasSuccessful = true;
// just send a response back that indications the communication worked
$response->message = "Title: {$_POST['title']}";

echo json_encode($response);

ColbyAjax::end();
