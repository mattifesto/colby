<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response   = new CBAjaxResponse();
$spec        = CBViewPage::specWithID($_POST['data-store-id']);

if (false !== $spec) {
    $response->modelJSON = json_encode($spec);
}

$response->wasSuccessful = true;

$response->send();
