<?php

final class CBAdminPageForUpdate {

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
        CBHTMLOutput::setTitleHTML('Update');
        CBHTMLOutput::setDescriptionHTML('Tools to perform site version updates.');
    }

    /**
     * @return bool
     */
    static function installationIsRequired() {
        $SQL = <<<EOT

            SELECT
                COUNT(*) AS `count`
            FROM
                `information_schema`.`TABLES`
            WHERE
                `TABLE_SCHEMA`  = DATABASE() AND
                `TABLE_NAME`    = 'CBDictionary'

EOT;

        $count = CBDB::SQLToValue($SQL);

        return $count > 0;
    }

    /**
     * @return null
     */
    static function pullUpdatesForAjax() {
        $response = new CBAjaxResponse();

        $response->description = "$ git pull\n";
        $result = CBGit::pull();
        $response->description .= $result->output;
        $response->descriptionFormat = 'preformatted';

        if ($result->wasSuccessful) {
            $response->description .= "\n\n$ git submodule update\n";

            $result = CBGit::submoduleUpdate();

            if ($result->wasSuccessful) {
                $response->message = 'Git pull and submodule update were successful.';
            } else {
                $response->message = 'Git pull was successful but submodue update failed.';
            }

            $response->description .= $result->output;
        } else {
            $response->message = 'Git pull failed.';
        }

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
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBUI', 'CBUIActionLink'];
    }

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @return null
     */
    static function update() {
        include Colby::findFile('setup/update.php');
        CBLog::addMessage('System', 5, 'The system was updated.');
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
