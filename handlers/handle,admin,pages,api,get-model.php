<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response                   = new CBAjaxResponse();
$specificationModel         = CBViewPage::specificationModelWithID($_POST['data-store-id']);

if (false !== $specificationModel) {
    $response->modelJSON    = json_encode($specificationModel);
}

$response->wasSuccessful    = true;

$response->send();
