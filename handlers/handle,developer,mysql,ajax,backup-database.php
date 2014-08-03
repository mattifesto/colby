<?php

if (!ColbyUser::current()->isOneOfThe('Developers'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response = new CBAjaxResponse();

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

$response->send();
