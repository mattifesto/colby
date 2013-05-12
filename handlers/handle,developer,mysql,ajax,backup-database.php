<?php

$response = new ColbyOutputManager('ajax-response');

$response->begin();

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    $response->message = 'You are not authorized to use this feature.';

    goto done;
}

/**
 * Ensure that the 'backup-files' directory exists
 */

$intraSiteDatabaseBackupsDirectory = '/tmp/database-backups';
$absoluteDatabaseBackupsDirectory = COLBY_SITE_DIRECTORY . "/{$intraSiteDatabaseBackupsDirectory}";

if (!is_dir($absoluteDatabaseBackupsDirectory))
{
    mkdir($absoluteDatabaseBackupsDirectory, 0777, true);
}

/**
 * Generate a filename for the database backup file.
 *
 * "dbname-database-backup.2013.05.11.123059"
 *
 * Y    A full numeric representation of a year, 4 digits
 * m    Numeric representation of a month, with leading zeros
 * d    Day of the month, 2 digits with leading zeros
 * H    24-hour format of an hour with leading zeros
 * i    Minutes with leading zeros
 * s    Seconds, with leading zeros
 */

$filename = $_SERVER['SERVER_NAME'] . '-' . time() . '.sql';
$intraSiteFilename = "{$intraSiteDatabaseBackupsDirectory}/{$filename}";
$absoluteFilename = COLBY_SITE_DIRECTORY . "/{$intraSiteFilename}";

/**
 * Generate the command and execute.
 */

$host = escapeshellarg(COLBY_MYSQL_HOST);
$user = escapeshellarg(COLBY_MYSQL_USER);
$password = escapeshellarg(COLBY_MYSQL_PASSWORD);
$database = escapeshellarg(COLBY_MYSQL_DATABASE);

$command = "mysqldump -h {$host} -u {$user} --password={$password} --databases {$database} --add-drop-database --extended-insert=FALSE --hex-blob --routines --result-file={$absoluteFilename}";

exec($command);

/**
 * Send the response
 */

$response->wasSuccessful = true;
$response->message = "The database was dumped to the file named: \"{$intraSiteFilename}\".";

done:

$response->end();
