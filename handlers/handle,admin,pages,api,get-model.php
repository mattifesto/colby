<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response = new CBAjaxResponse();

/**
 * Get parameter values.
 */

$dataStoreID = $_POST['data-store-id'];

/**
 * CBViewPage will load and upgrade the model including all of its subviews.
 * Because of this, the editor code can assume current models and doesn't need
 * duplicate any update code.
 */

$page                   = CBViewPage::initWithID($dataStoreID);
$model                  = $page->model();
$response->modelJSON    = json_encode($model);

/**
 * Send the response
 */

$response->wasSuccessful = true;

$response->send();
