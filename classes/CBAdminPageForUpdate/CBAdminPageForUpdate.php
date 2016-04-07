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
     * @return void
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
    public static function requiredJavaScriptURLs() {
        return [CBAdminPageForUpdate::URL('CBAdminPageForUpdate.js')];
    }

    /**
     * @return void
     */
    public static function update() {
        include Colby::findFile('setup/update.php');
    }

    /**
     * @return void
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
