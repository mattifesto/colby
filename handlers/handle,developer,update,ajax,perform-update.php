<?php

/**
 * This Ajax request is allowed to complete in two situations:
 *
 *  1. The POST variable `requestIsForInitialInstallation` is set and the system
 *     tables don't currently exist.
 *
 *  2. The user is in the "Developers" group.
 */

if (isset($_POST['requestIsForInitialInstallation']))
{
    $sql = <<<EOT

        SELECT
            COUNT(*) AS `count`
        FROM
            `information_schema`.`TABLES`
        WHERE
            `TABLE_SCHEMA`  = DATABASE() AND
            `TABLE_NAME`    = 'CBDictionary'

EOT;

    $result                         = Colby::query($sql);
    $initialInstallationIsRequired  = !$result->fetch_object()->databaseIsInstalled;

    $result->free();
}
else
{
    $initialInstallationIsRequired = false;
}

if (!$initialInstallationIsRequired &&
    !ColbyUser::current()->isOneOfThe('Developers'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response = new CBAjaxResponse();

if ($filename = Colby::findFile('setup/update.php'))
{
    include $filename;
}

/**
 * Send the response
 */

$response->wasSuccessful    = true;
$response->message          = "The site was successfully updated.";
$response->send();
