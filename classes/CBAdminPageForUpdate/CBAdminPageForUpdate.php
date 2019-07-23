<?php

final class CBAdminPageForUpdate {

    private static $installationIsRequired = false;

    /**
     * @return [string]
     */
    static function adminPageMenuNamePath() {
        return ['develop', 'update'];
    }


    /**
     * @return stdClass
     */
    static function adminPagePermissions() {
        return (object)['group' => 'Developers'];
    }


    /**
     * @return void
     */
    static function adminPageRenderContent() {
        CBHTMLOutput::pageInformation()->title = 'Update Website';
    }


    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBMaintenance',
            'CBMessageMarkup',
            'CBUI',
            'CBUIExpander',
            'CBUISection',
            'CBUISectionItem4',
            'CBUIStringsPart',
            'Colby',
        ];
    }


    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v488.js', cbsysurl()),
        ];
    }


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
            WHERE   `TABLE_SCHEMA`  = DATABASE() AND
                    `TABLE_NAME`    = 'ColbyPages'

EOT;

        if (CBDB::SQLToValue($SQL) == 0) {
            CBAdminPageForUpdate::$installationIsRequired = true;
            return true;
        } else {
            return false;
        }
    }


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
    static function CBAjax_pull_group(): string {
        return 'Developers';
    }


    /**
     * @return void
     */
    static function update(): void {
        include Colby::findFile('setup/update.php');
        CBLog::addMessage('System', 5, 'The system was updated.');

        CBAdminPageForUpdate::$installationIsRequired = false;
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
    static function CBAjax_update_group(): string {
        return 'Developers';
    }
    /* CBAjax_update_group() */
}
