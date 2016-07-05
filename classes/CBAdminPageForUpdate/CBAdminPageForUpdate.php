<?php

class CBAdminPageForUpdate {

    /**
     * @return bool
     */
    public static function installationIsRequired() {
        $sql = <<<EOT

            SELECT
                COUNT(*) AS `count`
            FROM
                `information_schema`.`TABLES`
            WHERE
                `TABLE_SCHEMA`  = DATABASE() AND
                `TABLE_NAME`    = 'CBDictionary'

EOT;

        $result                 = Colby::query($sql);
        $installationIsRequired = !$result->fetch_object()->count;

        $result->free();

        return $installationIsRequired;
    }

    /**
     * @return null
     */
    public static function pullUpdatesForAjax() {
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
    public static function pullUpdatesForAjaxPermissions() {
        return (object)['group' => 'Developers'];
    }

    /**
     * @return null
     */
    public static function renderAsHTML() {
        include __DIR__ . '/CBAdminPageForUpdateHTML.php';
    }

    /**
     * @return [string]
     */
    public static function requiredClassNames() {
        return ['CBUI', 'CBUIActionLink'];
    }

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [CBAdminPageForUpdate::URL('CBAdminPageForUpdate.css')];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [CBAdminPageForUpdate::URL('CBAdminPageForUpdate.js')];
    }

    /**
     * @return null
     */
    public static function update() {
        include Colby::findFile('setup/update.php');
        CBLog::addMessage('System', 5, 'The system was updated.');
    }

    /**
     * @return null
     */
    public static function updateForAjax() {
        $response = new CBAjaxResponse();

        self::update();

        $response->wasSuccessful    = true;
        $response->message          = "The site was successfully updated.";
        $response->send();
    }

    /**
     * @return stdClass
     */
    public static function updateForAjaxPermissions() {
        $permissions = new stdClass();

        if (isset($_POST['requestIsForInitialInstallation']) &&
            self::installationIsRequired()) {
            $permissions->group = 'Public';
        } else {
            $permissions->group = 'Developers';
        }

        return $permissions;
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
