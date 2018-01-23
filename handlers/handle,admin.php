<?php

if (CBAdminPageForUpdate::installationIsRequired()) {
    CBAdminPageForUpdate::update();
}

if (!ColbyUser::currentUserIsMemberOfGroup('Administrators')) {
    return include cbsysdir() . '/handlers/handle-authorization-failed.php';
}

$className = $_GET['c'] ?? 'CBStatusAdminPage';
$pageStub = $_GET['p'] ?? '';

CBAdmin::render($className, $pageStub);
