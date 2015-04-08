<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response       = new CBAjaxResponse();
$pageID         = $_POST['data-store-id'];
$pageIDAsSQL    = ColbyConvert::textToSQL($pageID);
$SQL            = <<<EOT

    SELECT
        `iteration`
    FROM
        `ColbyPages`
    WHERE
        `archiveID` = UNHEX('{$pageIDAsSQL}')

EOT;

$result     = Colby::query($SQL);
$row        = $result->fetch_object();

$result->free();

if (!$row) {
    throw new RuntimeException("No page was found for the page ID: {$pageID}");
}

$spec = CBViewPage::specWithID($pageID, $row->iteration);

if (!$spec) {
    throw new RuntimeException("No spec was found for the page ID: {$pageID}");
}

$response->modelJSON        = json_encode($spec);
$response->wasSuccessful    = true;

$response->send();
