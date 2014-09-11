<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response = new CBAjaxResponse();

$response->pages = array();

$page = new stdClass();
$page->title = "Hello, World! 1";

$response->pages[] = $page;

$page = new stdClass();
$page->title = "Hello, World! 2";

$response->pages[] = $page;

$page = new stdClass();
$page->title = "Hello, World! 3";

$response->pages[] = $page;

/**
 * Send the response
 */

$response->wasSuccessful = true;

$response->send();
