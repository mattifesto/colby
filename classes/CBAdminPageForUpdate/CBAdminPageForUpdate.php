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
            'CBUI',
            'CBUIExpander',
            'CBUISection',
            'CBUISectionItem4',
            'CBUIStringsPart',
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v443.js', cbsysurl())
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
     * @return null
     */
    static function pullUpdatesForAjax() {
        $response = new CBAjaxResponse();

        $description[] = "$ git pull";
        $result = CBGit::pull();
        $description[] = $result->output;

        if ($result->wasSuccessful) {
            $message[] = 'Git pull succeeded.';
        } else {
            $message[] = 'Git pull failed.';
        }

        $description[] = '$ git submodule update --init --recursive';

        $result = CBGit::submoduleUpdate();

        if ($result->wasSuccessful) {
            $message[] = 'Git submodule update succeeded.';
        } else {
            $message[] = 'Git submodule update failed.';
        }

        $description[] = $result->output;

        $response->description = implode("\n\n", $description);
        $response->descriptionFormat = 'preformatted';
        $response->message = implode(' ', $message);
        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return stdClass
     */
    static function pullUpdatesForAjaxPermissions() {
        return (object)['group' => 'Developers'];
    }

    /**
     * @return null
     */
    static function update() {
        include Colby::findFile('setup/update.php');
        CBLog::addMessage('System', 5, 'The system was updated.');

        CBAdminPageForUpdate::$installationIsRequired = false;
    }

    /**
     * @return null
     */
    static function updateForAjax() {
        $response = new CBAjaxResponse();

        CBAdminPageForUpdate::update();

        $response->wasSuccessful    = true;
        $response->message          = "The site was successfully updated.";
        $response->send();
    }

    /**
     * @return stdClass
     */
    static function updateForAjaxPermissions() {
        $permissions = new stdClass();

        if (isset($_POST['requestIsForInitialInstallation']) && CBAdminPageForUpdate::installationIsRequired()) {
            $permissions->group = 'Public';
        } else {
            $permissions->group = 'Developers';
        }

        return $permissions;
    }
}
