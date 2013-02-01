<?php

/**
 * 2013.01.31
 *
 * We check to see if the table `ColbyUsers` exists. If it does not exist we
 * do a full database install. If it does exists, we run the upgrades.
 *
 * We won't try to repair the odd situation that `ColbyUsers` does not exist
 * but the other tables do.
 *
 * Each of the upgrades performs its own heuristics to determine whether it
 * should attempt to make changes to the database.
 */

/**
 * TODO:
 *
 * -   Websites can override individual upgrades to include their own udgrades
 *     but they can't add an upgrade. They should be able to do that.
 *
 * -   We should have a way of determining that no upgrades are needed. Right
 *     now all of the upgrades are non destructive so it's not a problem but
 *     in the future it will be nice to have a way to say whether an upgrade
 *     is needed or not.
 */

$sql = <<<EOT
SELECT
    COUNT(*) AS `count`
FROM
    information_schema.TABLES
WHERE
    TABLE_SCHEMA = DATABASE() AND
    TABLE_NAME = 'ColbyUsers'
EOT;

$result = Colby::query($sql);

$colbyUsersTableDoesExist = $result->fetch_object()->count;

$result->free();

if (!$colbyUsersTableDoesExist)
{
    // No permissions are requiered for the initial installation.

    $response = ColbyOutputManager::beginAjaxResponse();

    /**
     * Run install.
     */

    include(Colby::findSnippet('install-database.php'));

    $response->message = 'The database schema was installed successfully.';
}
else
{
    // Verified user permissions required to upgrade.

    $response = ColbyOutputManager::beginVerifiedUserAjaxResponse();

    /**
     * Run upgrades.
     */

    include(Colby::findSnippet('upgrade-database-0001.php'));
    include(Colby::findSnippet('upgrade-database-0002.php'));
    include(Colby::findSnippet('upgrade-database-0003.php'));

    include(Colby::findSnippet('upgrade-database-version.php'));

    $response->message = 'The database schema was upgraded successfully.';
}

/**
 * Send response
 */

$response->wasSuccessful = true;
$response->end();
