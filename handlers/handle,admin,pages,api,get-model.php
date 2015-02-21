<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response                   = new CBAjaxResponse();
$response->modelJSON        = json_encode(CBViewPage::specificationModelWithID($_POST['data-store-id']));
$response->wasSuccessful    = true;

$response->send();
