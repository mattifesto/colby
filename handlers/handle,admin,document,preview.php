<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

$dataStoreID        = $_GET['archive-id'];
$iteration          = isset($_GET['iteration']) ? $_GET['iteration'] : null;
$dataStoreIDForSQL  = ColbyConvert::textToSQL($dataStoreID);
$SQL                = <<<EOT

    SELECT
        `className`,
        `iteration`,
        LOWER(HEX(`typeID`)) as `typeID`
    FROM
        `ColbyPages`
    WHERE
        `archiveID` = UNHEX('{$dataStoreIDForSQL}')

EOT;

$result     = Colby::query($SQL);
$row        = $result->fetch_object();
$iteration  = $iteration ? $iteration : $row->iteration;

$result->free();

if (!$row) {
    echo 'No page exists for the provided data store ID.';
    return 1;
}

$className = $row->className;

if (is_callable($function = "{$className}::renderAsHTMLForID")) {
    call_user_func($function, $dataStoreID, $iteration);
} else {
    /* Deprecated */
    $page = $className::initWithID($dataStoreID);
    $page->renderHTML();
}
