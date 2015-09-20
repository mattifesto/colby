<?php

/**
 * This class provides access to the most important site preferences. As such
 * the functions in this class return default values even if the site hasn't
 * been fully or properly installed or updated yet.
 *
 * Devlopers should call the functions on this class rather than accessing the
 * model directly so that the use of deprecated properties can be found and the
 * implementation of individual property calculation can change.
 */
final class CBSitePreferences {

    const ID                = '89b64c9cab5a6c28cfbfe0d2c1c7f97e9821f452';
    private static $model   = false;

    /**
     * Returns true if the site is in "debug" mode. Development and test sites
     * should generally have this property set to true and production sites
     * should not unless they are actively being investigated.
     *
     * A site should not be less secure because this property returns true. For
     * instance, a site shouldn't send passwords to debug log or reveal user
     * data to public pages in debug mode.
     *
     * This function returns true if the site preferences have not be properly
     * set up in an effort to both expose that fact and help find out why.
     *
     * @return {bool}
     */
    public static function debug() {
        $model = CBSitePreferences::model();
        return $model->debug;
    }

    /**
     * @return {bool}
     */
    public static function disallowRobots() {

        /**
         * @TODO 2015.09.20
         * Remove CBShouldDisallowRobots from all sites and remove this code.
         */
        if (defined('CBShouldDisallowRobots')) {
            return !!CBShouldDisallowRobots;
        }

        $model = CBSitePreferences::model();
        return $model->disallowRobots;
    }

    /**
     * @return [{string}]
     */
    public static function editorURLsForCSS() {
        return [
            CBSitePreferences::URL('CBSitePreferencesEditor.css')
        ];
    }

    /**
     * @return [{string}]
     */
    public static function editorURLsForJavaScript() {
        return [
            CBSystemURL . '/javascript/CBBooleanEditorFactory.js',
            CBSystemURL . '/javascript/CBStringEditorFactory.js',
            CBSitePreferences::URL('CBSitePreferencesEditorFactory.js')
        ];
    }

    /**
     * @return  {string}
     *  Returns a Google Tag Manager ID or an empty string.
     */
    public static function googleTagManagerID() {
        $model = CBSitePreferences::model();

        return $model->googleTagManagerID;
    }

    /**
     * @return null
     */
    public static function install() {
        $spec = CBModels::fetchSpecByID(CBSitePreferences::ID);

        if ($spec === false) {
            $spec           = CBModels::modelWithClassName(__CLASS__, ['ID' => CBSitePreferences::ID]);

            CBModels::save([$spec]);
        }
    }

    /**
     * @return null
     */
    private static function model() {
        if (CBSitePreferences::$model === false) {
            $filepath = CBDataStore::filepath([
                'ID'        => CBSitePreferences::ID,
                'filename'  => 'site-preferences.json'
            ]);

            if (is_file($filepath)) {
                $model = json_decode(file_get_contents($filepath));
            } else {
                $model = CBSitePreferences::specToModel(new stdClass());
            }

            // 2015.09.19 googleAnalyticsTrackingID
            if (!isset($model->googleTagManagerID)) {
                $model->googleTagManagerID = '';
            }

            // 2015.09.20 disallowRobots
            if (!isset($model->disallowRobots)) {
                $model->disallowRobots = false;
            }

            CBSitePreferences::$model = $model;
        }

        return CBSitePreferences::$model;
    }

    /**
     * This function saves the model to disk so that the database doesn't have
     * to be involved to use the information. Since this data is such core
     * system data this also avoids race conditions. For instance, if the
     * database is down we can still get the information needed to send an
     * alert email.
     *
     * @return null
     */
    public static function modelsWillSave(array $tuples) {
        array_map(function($tuple) {
            $filepath   = CBDataStore::filepath([
                'ID'        => $tuple->spec->ID,
                'filename'  => 'site-preferences.json'
            ]);

            CBDataStore::makeDirectoryForID($tuple->spec->ID);
            file_put_contents($filepath, json_encode($tuple->model));
        }, $tuples);
    }

    /**
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model                      = CBModels::modelWithClassName(__CLASS__);
        $model->debug               = isset($spec->debug) ? !!$spec->debug : false;
        $model->disallowRobots      = isset($spec->disallowRobots) ? !!$spec->disallowRobots : false;
        $model->googleTagManagerID  = isset($spec->googleTagManagerID) ? trim($spec->googleTagManagerID) : '';

        return $model;
    }

    /**
     * @return {string}
     */
    public static function URL($filename) {
        return CBSystemURL . "/classes/CBSitePreferences/{$filename}";
    }
}
