<?php

if (
    !CBUserGroup::userIsMemberOfUserGroup(
        ColbyUser::getCurrentUserCBID(),
        'CBAdministratorsUserGroup'
    )
) {
    return include cbsysdir() . '/handlers/handle-authorization-failed.php';
}

$modelToExportID = cb_query_string_value('ID');
$spec = CBModels::fetchSpecByID($modelToExportID);
$specAsJSON = json_encode($spec, JSON_PRETTY_PRINT);
$server = $_SERVER['SERVER_NAME'];
$title = empty(trim($spec->title)) ? 'Untitled Model' : $spec->title;
$filename = rawurlencode("{$title} ({$spec->className}, {$server}).json");

header('Pragma: public');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private', false); // required for certain browsers
header('Content-Type: application/json');
header("Content-Disposition: attachment; filename=\"model.json\"; filename*=UTF-8''{$filename}");
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . strlen($specAsJSON));

echo $specAsJSON;
