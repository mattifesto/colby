<?php

$colbyTestSiteVersionSchemaNumber = 42;

/**
 * Determine whether the `ColbySiteSchemaVersionNumber` function exists in the
 * database.
 */

$sql = <<<EOT
SELECT COUNT(*) AS `siteVersionFunctionDoesExist`
FROM
    `information_schema`.`ROUTINES`
WHERE
    `ROUTINE_SCHEMA` = DATABASE() AND
    `ROUTINE_TYPE` = 'FUNCTION' AND
    `ROUTINE_NAME` = 'ColbySiteSchemaVersionNumber'
EOT;

$result = Colby::query($sql);

$siteVersionFunctionDidOriginallyExist = $result->fetch_object()->siteVersionFunctionDoesExist;

$result->free();

/**
 * Get the version number as it exists before testing.
 */

$originalVersionNumber = Colby::siteSchemaVersionNumber();

/**
 * Set a new test version number.
 */

Colby::setSiteSchemaVersionNumber($colbyTestSiteVersionSchemaNumber);

/**
 * Make sure the version number has been set correctly.
 */

$returnedVersionNumber = Colby::siteSchemaVersionNumber();

if ($colbyTestSiteVersionSchemaNumber != $returnedVersionNumber)
{
    throw new RuntimeException("The `Colby::siteSchemaVersionNumber` method is returning the wrong value: {$returnedVersionNumber}; it should return the value set using the `Colby::setSiteSchemaVersionNumber` method: {$colbyTestSiteVersionSchemaNumber}.");
}

/**
 * Remove the `ColbySiteSchemaVersionNumber` database function and make sure
 * that the `Colby::siteSchemaVersionNumber` method returns 0.
 */

$sql = 'DROP FUNCTION ColbySiteSchemaVersionNumber';

COLBY::query($sql);

$returnedVersionNumber = Colby::siteSchemaVersionNumber();

if (0 != $returnedVersionNumber)
{
    throw new RuntimeException('The `Colby::siteSchemaVersionNumber` should return 0 when there is no `ColbySiteSchemaVersionNumber` function in the database.');
}

/**
 * Make sure that there is still no `ColbySiteSchemaVersionNumber` function in
 * the database.
 */

$sql = <<<EOT
SELECT COUNT(*) AS `siteVersionFunctionDoesExist`
FROM
    `information_schema`.`ROUTINES`
WHERE
    `ROUTINE_SCHEMA` = DATABASE() AND
    `ROUTINE_TYPE` = 'FUNCTION' AND
    `ROUTINE_NAME` = 'ColbySiteSchemaVersionNumber'
EOT;

$result = Colby::query($sql);

$functionDoesExist = $result->fetch_object()->siteVersionFunctionDoesExist;

$result->free();

if ($functionDoesExist)
{
    throw new RuntimeException('The `Colby::siteSchemaVersionNumber` method should not create the `ColbySiteSchemaVersionNumber` database function.');
}

/**
 * Return the system to its original state.
 */

if ($siteVersionFunctionDidOriginallyExist)
{
    Colby::setSiteSchemaVersionNumber($originalVersionNumber);
}

$returnedVersionNumber = Colby::siteSchemaVersionNumber();

if ($returnedVersionNumber != $originalVersionNumber)
{
    throw new RuntimeException("When trying to return the system to its original state the `Colby::siteSchemaVersionNumber` method is returning: {$returnedVersionNumber}; it should return the original version number: {$originalVersionNumber}.");
}
