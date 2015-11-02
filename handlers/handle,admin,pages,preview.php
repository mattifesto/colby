<?php

if (!ColbyUser::current()->isOneOfThe('Administrators')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
}

$ID = $_GET['ID'];
$iteration = isset($_GET['iteration']) ? $_GET['iteration'] : null;
$IDAsSQL = CBHex160::toSQL($ID);
$SQL = <<<EOT

    SELECT      `p`.`className`,
                `p`.`iteration`,
                `v`.`modelAsJSON` as `model`
    FROM        `ColbyPages`        AS `p`
    LEFT JOIN   `CBModels`          AS `m` ON `p`.`archiveID` = `m`.`ID`
    LEFT JOIN   `CBModelVersions`   AS `v` ON `m`.`ID` = `v`.`ID` AND `m`.`version` = `v`.`version`
    WHERE       `p`.`archiveID` = {$IDAsSQL}

EOT;

$data = CBDB::SQLToObject($SQL);

if ($data === false) {
    echo 'No page exists for the provided data store ID.';
    return 1;
} else {
    $data->model = json_decode($data->model);
}

if ($data->model && is_callable($function = "{$data->className}::renderModelAsHTML")) {
    call_user_func($function, $data->model);
} else {
    call_user_func("{$data->className}::renderAsHTMLForID", $ID, $data->iteration);
}
