<?php

// This ajax does not require a verified user, so it must either run only when appropriate or be non-destructive.

Colby::useAjax();

ColbyAjax::begin();

$result = new stdClass();
$result->wasSuccessful = false;
$result->message = 'incomplete';

echo json_encode($result);

ColbyAjax::end();
