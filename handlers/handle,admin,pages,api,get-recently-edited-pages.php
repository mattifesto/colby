<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response = new CBAjaxResponse();

$response->pages = array();

$page = new stdClass();
$rand = bin2hex(openssl_random_pseudo_bytes(1));
$page->title = "Hello, World! {$rand}";

$response->pages[] = $page;

$page = new stdClass();
$rand = bin2hex(openssl_random_pseudo_bytes(1));
$page->title = "Hello, World! {$rand}";

$response->pages[] = $page;

$page = new stdClass();
$rand = bin2hex(openssl_random_pseudo_bytes(1));
$page->title = "Hello, World! {$rand}";

$response->pages[] = $page;

/**
 * Send the response
 */

$response->wasSuccessful = true;

$response->send();
