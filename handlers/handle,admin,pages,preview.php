<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

$ID         = $_GET['ID'];
$iteration  = isset($_GET['iteration']) ? $_GET['iteration'] : null;
$IDForSQL   = CBHex160::toSQL($ID);
$SQL        = <<<EOT

    SELECT
        `className`,
        `iteration`
    FROM
        `ColbyPages`
    WHERE
        `archiveID` = {$IDForSQL}

EOT;

$result     = Colby::query($SQL);
$row        = $result->fetch_object();
$iteration  = $iteration ? $iteration : $row->iteration;

$result->free();

if (!$row) {
    echo 'No page exists for the provided data store ID.';
    return 1;
}

call_user_func("{$row->className}::renderAsHTMLForID", $ID, $iteration);
