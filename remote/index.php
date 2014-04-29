<?php

define('CBSiteDirectory', $_SERVER['DOCUMENT_ROOT']);
define('CBSystemDirectory', CBSiteDirectory . '/colby');

include CBSystemDirectory . '/classes/CBEncryptedResponse.php';

$response                   = new CBEncryptedResponse();
$response->message          = "This is the message";
$response->wasSuccessful    = true;
$response->send();
