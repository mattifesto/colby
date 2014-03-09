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
 * -   Websites can override individual upgrades to include their own upgrades
 *     but they can't add an upgrade. They should be able to do that.
 *
 * -   We should have a way of determining that no upgrades are needed. Right
 *     now all of the upgrades are non destructive so it's not a problem but
 *     in the future it will be nice to have a way to say whether an upgrade
 *     is needed or not.
 */

/**
 * No permissions are required for the initial installation. If it turns out
 * this is an upgrade request we will discard this response and begin a new
 * response that requires authentication.
 */

$response = new ColbyOutputManager('ajax-response');

$response->begin();

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
    /**
     * Run install.
     */

    include Colby::findSnippet('install-database.php');

    $response->message = 'The database schema was installed successfully.';
}
else
{
    $response->message = 'The database schema has already been installed or partially installed.';
}

/**
 * Send response
 */

$sql = 'SELECT ColbySchemaVersionNumber() AS `schemaVersionNumber`';

$result = Colby::query($sql);

$response->schemaVersionNumber = $result->fetch_object()->schemaVersionNumber;

$result->free();

$response->wasSuccessful = true;

done:

$response->end();
