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

    const ID = '89b64c9cab5a6c28cfbfe0d2c1c7f97e9821f452';

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
            CBSitePreferences::URL('CBSitePreferencesEditorFactory.js')
        ];
    }

    /**
     * @return null
     */
    public static function install() {
        $spec = CBModels::fetchSpecByID(CBSitePreferences::ID);

        if ($spec === false) {
            $spec           = CBModels::modelWithClassName(__CLASS__, ['ID' => CBSitePreferences::ID]);
            $spec->debug    = false;

            CBModels::save([$spec]);
        }
    }

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
        $model = CBModelCache::fetchModelByID(CBSitePreferences::ID);
        return isset($model->debug) ? $model->debug : true;
    }

    /**
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model          = CBModels::modelWithClassName(__CLASS__);
        $model->debug   = isset($spec->debug) ? !!$spec->debug : false;

        return $model;
    }

    /**
     * @return {string}
     */
    public static function URL($filename) {
        return CBSystemURL . "/classes/CBSitePreferences/{$filename}";
    }
}
