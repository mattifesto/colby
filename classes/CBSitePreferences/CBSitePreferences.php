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
     * @param string $key
     *
     * @return mixed|null
     */
    public static function customValueForKey($key) {
        $model = CBSitePreferences::model();

        if (isset($model->custom->{$key})) {
            return $model->custom->{$key};
        } else {
            return null;
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
     * @return bool
     */
    public static function debug() {
        $model = CBSitePreferences::model();
        return isset($model->debug) ? ($model->debug === true) : false;
    }

    /**
     * This function is used by the CBHTMLOutput class.
     *
     * @return string
     *  Returns an empty string if unset for legacy reasons.
     */
    public static function defaultClassNameForPageSettings() {
        $model = CBSitePreferences::model();
        return empty($model->defaultClassNameForPageSettings) ? '' : $model->defaultClassNameForPageSettings;
    }

    /**
     * @return bool
     */
    public static function disallowRobots() {
        $model = CBSitePreferences::model();
        return isset($model->disallowRobots) ? ($model->disallowRobots === true) : false;
    }

    /**
     * @return hex160?
     */
    public static function frontPageID() {
        $model = CBSitePreferences::model();

        if (isset($model->frontPageID)) {
            return $model->frontPageID;
        } else {
            $filepath = CBDataStore::filepath([ /* deprecated */
                'ID' => CBPageTypeID,
                'filename' => 'front-page.json'
            ]);

            if (file_exists($filepath)) {
                $frontPage = json_decode(file_get_contents($filepath));
                return $frontPage->dataStoreID;
            } else {
                return null;
            }
        }
    }

    /**
     * @return string
     *  Returns an empty string if unset for legacy reasons.
     */
    public static function googleTagManagerID() {
        $model = CBSitePreferences::model();
        return empty($model->googleTagManagerID) ? '' : $model->googleTagManagerID;
    }

    /**
     * @return {stdClass}
     */
    public static function info() {
        return CBModelClassInfo::specToModel((object)[
            'pluralTitle' => 'Site Preferences',
            'singularTitle' => 'Site Preferences'
        ]);
    }

    /**
     * Re-saving these preferences each update ensures that the model always has
     * valid values for all properties without having to add a new update script
     * each time the properties change.
     *
     * @return null
     */
    public static function install() {
        $spec = CBModels::fetchSpecByID(CBSitePreferences::ID);

        if ($spec === false) {
            $spec = CBModels::modelWithClassName(__CLASS__, ['ID' => CBSitePreferences::ID]);
        }

        CBModels::save([$spec]);
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
     * @param hex160 $ID
     *
     * @return null
     */
    public static function setFrontPageID($ID) {
        if (!CBHex160::is($ID)) {
            throw new Exception("'{$ID}' is not a 160-bit hexadecimal value.");
        }

        $spec = CBModels::fetchSpecByID(CBSitePreferences::ID);
        $spec->frontPageID = $ID;
        CBModels::save([$spec]);

        /* clear cache */
        CBSitePreferences::$model = false;

        /* remove deprecated data store */
        CBDataSTore::deleteByID(CBPageTypeID);
    }

    /**
     * @return null
     */
    public static function setFrontPageIDForAjax() {
        $response = new CBAjaxResponse();
        $ID = $_POST['ID'];

        CBSitePreferences::setFrontPageID($ID);

        $response->message = "The front page was changed successfully.";
        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return stdClass
     */
    public static function setFrontPageIDForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model = CBModels::modelWithClassName(__CLASS__);
        $model->debug = isset($spec->debug) ? !!$spec->debug : false;
        $model->defaultClassNameForPageSettings = isset($spec->defaultClassNameForPageSettings) ? trim($spec->defaultClassNameForPageSettings) : '';
        $model->disallowRobots = isset($spec->disallowRobots) ? !!$spec->disallowRobots : false;
        $model->frontPageID = CBModel::value($spec, 'frontPageID');
        $model->googleTagManagerID = isset($spec->googleTagManagerID) ? trim($spec->googleTagManagerID) : '';

        if (isset($spec->custom) && is_array($spec->custom)) {
            $model->custom = new stdClass();
            $keyValueModels = array_filter($spec->custom, function ($spec) { return $spec->className === 'CBKeyValuePair'; });
            $keyValueModels = array_map('CBModel::specToOptionalModel', $keyValueModels);
            $keyValueModels = array_filter($keyValueModels, function ($model) { return !empty($model->key); });

            foreach ($keyValueModels as $keyValueModel) {
                $model->custom->{$keyValueModel->key} = $keyValueModel->value;
            }
        }

        return $model;
    }

    /**
     * @return {string}
     */
    public static function URL($filename) {
        return CBSystemURL . "/classes/CBSitePreferences/{$filename}";
    }
}
