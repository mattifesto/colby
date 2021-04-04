<?php

final class CBAdminPageForUpdate {

    private static $installationIsRequired = false;



    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return string
     */
    static function CBAdmin_getUserGroupClassName(): string {
        return 'CBDevelopersUserGroup';
    }



    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath() {
        return [
            'develop',
            'update',
        ];
    }



    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Update Website';
    }



    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBAjax_backupDatabase(): void {
        CBAdminPageForUpdate::backupDatabase();
    }
    /* CBAjax_backupDatabase() */



    /**
     * @return string
     */
    static function
    CBAjax_backupDatabase_getUserGroupClassName(
    ): string {
        return 'CBDevelopersUserGroup';
    }
    /* CBAjax_backupDatabase_getUserGroupClassName() */



    /**
     * @return object
     *
     *      {
     *          output: string
     *          succeeded: bool
     *      }
     */
    static function CBAjax_pull(): stdClass {
        $output = [];

        CBGit::pull($output, $exitCode);

        if (empty($exitCode)) {
            CBGit::submoduleUpdate($output, $exitCode);
        }

        return (object)[
            'output' => implode("\n", $output),
            'succeeded' => empty($exitCode),
        ];
    }
    /* CBAjax_pull() */



    /**
     * @return string
     */
    static function CBAjax_pull_getUserGroupClassName(): string {
        return 'CBDevelopersUserGroup';
    }



    /**
     * @return object
     */
    static function CBAjax_pullColby(): stdClass {
        $originalDirectory = getcwd();

        if ($originalDirectory === false) {
            throw new CBException(
                'getcwd() returned false',
                '',
                '7d55fc39bd8246bc6f0f0131aedb12000cffbfd8'
            );
        }

        $colbyDirectory = cbsysdir();

        $output = [
            "$ cd {$colbyDirectory}",
            '',
        ];

        chdir($colbyDirectory);

        CBExec::exec(
            'git pull',
            $output,
            $exitCode
        );

        chdir($originalDirectory);

        return (object)[
            'output' => implode("\n", $output),
            'succeeded' => empty($exitCode),
        ];
    }
    /* CBAjax_pullColby() */



    /**
     * @return string
     */
    static function CBAjax_pullColby_getUserGroupClassName(): string {
        return 'CBDevelopersUserGroup';
    }



    /**
     * @return void
     */
    static function CBAjax_update(): void {
        CBAdminPageForUpdate::update();
    }
    /* CBAjax_update() */



    /**
     * @return string
     */
    static function CBAjax_update_getUserGroupClassName(): string {
        return 'CBDevelopersUserGroup';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.21.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        return [
            [
                'CBAdminPageForUpdate_isDevelopmentWebsite',
                CBSitePreferences::getIsDevelopmentWebsite(),
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBAjax',
            'CBMaintenance',
            'CBMessageMarkup',
            'CBUI',
            'CBUIExpander',
            'CBUINavigationView',
            'CBUIPanel',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $updater = CBModelUpdater::fetch(
            (object)[
                'ID' => CBDevelopAdminMenu::getModelCBID(),
            ]
        );

        $items = CBModel::valueToArray(
            $updater->working,
            'items'
        );

        array_push(
            $items,
            (object)[
                'className' => 'CBMenuItem',
                'name' => 'update',
                'text' => 'Update',
                'URL' => CBAdmin::getAdminPageURL(
                    'CBAdminPageForUpdate'
                ),
            ]
        );

        $updater->working->items = $items;

        CBModelUpdater::save($updater);
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBDevelopAdminMenu',
            'CBModelUpdater',
        ];
    }



    /* -- functions -- -- -- -- -- */



    /**
     * @return void
     */
    static function backupDatabase(): void {
        $intraSiteDatabaseBackupsDirectory = 'tmp/database-backups';

        $absoluteDatabaseBackupsDirectory = (
            cbsitedir() .
            "/{$intraSiteDatabaseBackupsDirectory}"
        );

        if (!is_dir($absoluteDatabaseBackupsDirectory)) {
            mkdir($absoluteDatabaseBackupsDirectory, 0777, true);
        }

        CBAdminPageForUpdate::removeOldBackupFiles(
            $absoluteDatabaseBackupsDirectory
        );

        /**
         * Generate a filename for the database backup file.
         */

        $time = time();
        $date = gmdate("Y_m_d", $time);

        $domain = preg_replace(
            '/\\./',
            '_',
            $_SERVER['SERVER_NAME']
        );

        $filename = "{$domain}_{$date}_{$time}.sql";
        $intraSiteFilename = "{$intraSiteDatabaseBackupsDirectory}/{$filename}";
        $absoluteFilename = cbsitedir() . "/{$intraSiteFilename}";

        /**
         * Generate the command and execute.
         */

        $host = escapeshellarg(CBSitePreferences::mysqlHost());
        $user = escapeshellarg(CBSitePreferences::mysqlUser());
        $password = escapeshellarg(CBSitePreferences::mysqlPassword());
        $database = escapeshellarg(CBSitePreferences::mysqlDatabase());
        $output = [];

        /**
         * @NOTE 2015_02_10
         *
         *      I'm having trouble with development environments on Mac OS X
         *      Yosemite not loading the `.bash_profile`. This means that
         *      `mysqldump` is not in the path. Set `CBMySQLDirectory` in the
         *      `colby-configuration.php` file to the directory that contains
         *      the MySQL binaries to work around whatever is happening here.
         */

        $command = 'mysqldump';

        $command =
        defined('CBMySQLDirectory') ?
        CBMySQLDirectory . "/{$command}" :
        $command;

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
                 *      Since I added code to save a call stack with each log
                 *      entry the log table has been growing substantially.
                 *      There is future work to be done here, but in the
                 *      meantime it is making database backups take forever when
                 *      the CBLog table isn't that important in that context.
                 *      The CBLog table will not be backed up for now and with
                 *      future mitigations we may add it back in, but probably
                 *      not.
                 */
                "--ignore-table={$database}.CBLog",
            ]
        );

        exec($command, $output, $result);

        if ($result) {
            throw new CBException(
                "An error occurred: {$result}\n\n" .
                "Output:\n" .
                implode("\n", $output),
                '',
                'd28b6f6cde9fc82b96ee5ca05b5da7c9651c8d95'
            );
        }
    }
    /* backupDatabase() */



    /**
     * @return bool
     *
     *      Returns true if the Colby tables need to be installed; otherwise
     *      false.
     */
    static function installationIsRequired() {
        if (CBAdminPageForUpdate::$installationIsRequired) {
            return true;
        }

        $SQL = <<<EOT

            SELECT  COUNT(*) AS `count`
            FROM    `information_schema`.`TABLES`
            WHERE   `TABLE_SCHEMA` = DATABASE() AND
                    `TABLE_NAME` = 'ColbyPages'

        EOT;

        if (CBDB::SQLToValue($SQL) == 0) {
            CBAdminPageForUpdate::$installationIsRequired = true;
            return true;
        } else {
            return false;
        }
    }
    /* installationIsRequired() */



    /**
     * @param string $directory
     *
     * @return void
     */
    private static function removeOldBackupFiles(string $directory): void {
        $files = array_map(
            function ($filepath) {
                return (object)[
                    'filepath' => $filepath,
                    'modified' => filemtime($filepath),
                ];
            },
            glob("{$directory}/*.sql")
        );

        if (empty($files)) {
            return;
        }

        usort(
            $files,
            function($a, $b) {
                if ($a->modified < $b->modified) {
                    return -1;
                } else if ($a->modified === $b->modified) {
                    return 0;
                } else {
                    return 1;
                }
            }
        );

        $countOfFiles = count($files);
        $minCountOfFiles = 3;
        $thirtyDaysAgo = time() - (60 * 60 * 24 * 30); /* 30 days */

        foreach ($files as $file) {
            if (
                $countOfFiles > $minCountOfFiles &&
                !empty($file->modified) &&
                $file->modified < $thirtyDaysAgo
            ) {
                unlink($file->filepath);
                $countOfFiles -= 1;
            }
        }
    }
    /* removeOldBackupFiles() */



    /**
     * @return void
     */
    static function
    update(
    ): void {
        include Colby::findFile(
            'setup/update.php'
        );

        CBLog::addMessage(
            'System',
            5,
            'The system was updated.'
        );

        CBAdminPageForUpdate::$installationIsRequired = false;
    }
    /* update() */

}
