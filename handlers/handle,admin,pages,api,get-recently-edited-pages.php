<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response = new CBAjaxResponse();

$tuple = CBDictionaryTuple::initWithKey('CBRecentlyEditedPages');

if ($tuple->value) {

    $response->pages = $tuple->value;

} else {

    $response->pages = array();
}

/**
 * Send the response
 */

$response->wasSuccessful = true;

$response->send();
