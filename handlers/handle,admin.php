<?php

if (CBAdminPageForUpdate::installationIsRequired()) {
    CBAdminPageForUpdate::update();
}

$isAdministrator = CBUserGroup::userIsMemberOfUserGroup(
    ColbyUser::getCurrentUserCBID(),
    'CBAdministratorsUserGroup'
);

if (!$isAdministrator) {
    return include cbsysdir() . '/handlers/handle-authorization-failed.php';
}

$className = $_GET['c'] ?? 'CBStatusAdminPage';
$pageStub = $_GET['p'] ?? '';

CBAdmin::render(
    $className,
    $pageStub
);
