<?php

if (!ColbyUser::current()->isOneOfThe('Developers')) {
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response = new CBAjaxResponse();

/**
 * Ensure that the 'backup-files' directory exists
 */

$intraSiteDatabaseBackupsDirectory = 'tmp/database-backups';
$absoluteDatabaseBackupsDirectory = cbsitedir() . "/{$intraSiteDatabaseBackupsDirectory}";

if (!is_dir($absoluteDatabaseBackupsDirectory)) {
    mkdir($absoluteDatabaseBackupsDirectory, 0777, true);
}

removeOldBackupFiles($absoluteDatabaseBackupsDirectory);

/**
 * Generate a filename for the database backup file.
 */

$time = time();
$date = gmdate("Y.m.d", $time);
$filename = $_SERVER['SERVER_NAME'] . "-{$date}.{$time}.sql";
$intraSiteFilename = "{$intraSiteDatabaseBackupsDirectory}/{$filename}";
$absoluteFilename = COLBY_SITE_DIRECTORY . "/{$intraSiteFilename}";

/**
 * Generate the command and execute.
 */

$host       = escapeshellarg(CBSitePreferences::mysqlHost());
$user       = escapeshellarg(CBSitePreferences::mysqlUser());
$password   = escapeshellarg(CBSitePreferences::mysqlPassword());
$database   = escapeshellarg(CBSitePreferences::mysqlDatabase());
$output     = array();

/**
 * 2015.02.10
 * I'm having trouble with development environments on Mac OS X Yosemite not
 * loading the `.bash_profile`. This means that `mysqldump` is not in the path.
 * Set `CBMySQLDirectory` in the `colby-configuration.php` file to the directory
 * that contains the MySQL binaries to work around whatever is happening here.
 */

$command = 'mysqldump';
$command = defined('CBMySQLDirectory') ? CBMySQLDirectory . "/{$command}" : $command;
$command = implode(
    ' ',
    [
        "{$command} -h {$host} -u {$user} --password={$password}",
        "--databases {$database}",
        "--add-drop-database",
        "--extended-insert=FALSE",
        "--hex-blob",
        "--routines",
        "--result-file={$absoluteFilename}",

        /**
         * @NOTE 2018_10_12
         *
         *      Since I added code to save a call stack with each log entry the
         *      log table has been growing substantially. There is future work
         *      to be done here, but in the meantime it is making database
         *      backups take forever when the CBLog table isn't that important
         *      in that context. The CBLog table will not be backed up for now
         *      and with future mitigations we may add it back in, but probably
         *      not.
         */
        "--ignore-table={$database}.CBLog",
    ]
);

exec($command, $output, $result);

if ($result) {
    $response->message = "An error occurred: {$result}\n\n" .
                         "Output:\n" .
                         implode("\n", $output);
    $response->send();

    return 1;
}

/**
 * Send the response
 */

$response->wasSuccessful = true;
$response->message = "The database was dumped to the file named: \"{$intraSiteFilename}\".";

$response->send();

/**
 * @param string $directory
 *
 * @return null
 */
function removeOldBackupFiles($directory) {
    $files = array_map(function ($filepath) {
        return (object)[
            'filepath' => $filepath,
            'modified' => filemtime($filepath),
        ];
    }, glob("{$directory}/*.sql"));

    if (empty($files)) {
        return;
    }

    usort($files, function($a, $b) {
        if ($a->modified < $b->modified) {
            return -1;
        } else if ($a->modified === $b->modified) {
            return 0;
        } else {
            return 1;
        }
    });

    $countOfFiles = count($files);
    $minCountOfFiles = 3;
    $thirtyDaysAgo = time() - (60 * 60 * 24 * 30); /* 30 days */

    foreach ($files as $file) {
        if ($countOfFiles > $minCountOfFiles && !empty($file->modified) && $file->modified < $thirtyDaysAgo) {
            unlink($file->filepath);
            $countOfFiles -= 1;
        }
    }
}
