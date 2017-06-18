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
    const defaultResizeOperations = [
        'rs200clc200',  /*  100 x 100 */
        'rw320',        /*  160 x ? */
        'rw640',        /*  320 x ? */
        'rw960',        /*  480 x ? */
        'rw1280',       /*  640 x ? */
        'rw1600',       /*  800 x ? */
        'rw1920',       /*  960 x ? */
        'rw2560',       /* 1280 x ? */
    ];

    private static $model = false;

    /**
     * return [string]
     */
    static function administratorEmails() {
        $model = CBSitePreferences::model();
        $emails = CBModel::value($model, 'administratorEmails', []);

        if (!empty($emails)) {
            return $emails;
        } else if (defined('CBSiteAdministratorEmail')) { // @deprecated
            return [CBSiteAdministratorEmail];
        } else if (defined('COLBY_SITE_ADMINISTRATOR')) { // @deprecated
            return [COLBY_SITE_ADMINISTRATOR];
        } else {
            return [];
        }
    }

    /**
     * The value of this setting should be changed directly on the spec by
     * adjusting the array value of classNamesForUserSettings.
     *
     * @return [string]
     */
    static function classNamesForUserSettings() {
        $model = CBSitePreferences::model();

        if (empty($model->classNamesForUserSettings)) {
            return [];
        } else {
            return $model->classNamesForUserSettings;
        }
    }

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
     * 2016.10.16 The comment above uses deprecated logic. The model should be
     * valid whether properties are set or not. However this may not yet be
     * the case.
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
     * This function should be called by installation functions that want to add
     * a class name for user settings.
     *
     * @param string $className
     *
     * @return null
     */
    static function installClassNameForUserSettings($className) {
        $spec = CBModels::fetchSpecByID(CBSitePreferences::ID);

        // We assume spec has been saved by the CBSitePreferences::install()
        // function. If not, we want to let the error occur as notification.

        if (empty($spec->classNamesForUserSettings)) {
            $spec->classNamesForUserSettings = [$className];
        } else {
            if (in_array($className, $spec->classNamesForUserSettings)) {
                return;
            } else {
                $spec->classNamesForUserSettings[] = $className;
            }
        }

        CBModels::save([$spec]);
    }

    /**
     * @return stdClass
     *
     *  Properties:
     *
     *      string? facebookURL
     *      hex160? frontPageID
     *      string? twitterURL
     */
    static function model() {
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
     * @return [string]
     *  An array of image resize operations that are allowed to be completed
     *  when the specific image size URL is first requested.
     */
    static function onDemandImageResizeOperations() {
        $model = CBSitePreferences::model();
        $operations = CBModel::value($model, 'onDemandImageResizeOperations', [], function ($value) {
            $operations = preg_split('/[\s,]+/', $value, -1, PREG_SPLIT_NO_EMPTY);
            return is_array($operations) ? $operations : [];
        });
        return array_unique(array_merge($operations, CBSitePreferences::defaultResizeOperations));
    }

    /**
     * @return string|null
     */
    public static function reCAPTCHASecretKey() {
        $model = CBSitePreferences::model();
        return empty($model->reCAPTCHASecretKey) ? null : $model->reCAPTCHASecretKey;
    }

    /**
     * @return string|null
     */
    public static function reCAPTCHASiteKey() {
        $model = CBSitePreferences::model();
        return empty($model->reCAPTCHASiteKey) ? null : $model->reCAPTCHASiteKey;
    }

    /**
     * @return bool
     *  Returns true if the site should send emails to the administrator when
     *  errors occur. This function does not indicate whether email sending is
     *  enabled for this website.
     */
    static function sendEmailsForErrors() {
        if (defined('CBSiteDoesSendEmailErrorReports')) {
            return !!CBSiteDoesSendEmailErrorReports;
        }

        /* deprecated */
        if (defined('COLBY_SITE_ERRORS_SEND_EMAILS')) {
            return !!COLBY_SITE_ERRORS_SEND_EMAILS;
        }

        return false;
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
     * @return string
     */
    static function siteDirectory() {
        return CBSiteDirectory;
    }

    /**
     * @return string
     */
    static function siteDomainName() {
        return explode('//', CBSiteURL)[1];
    }

    /**
     * @return string
     */
    static function siteName() {
        $model = CBSitePreferences::model();
        $siteName = CBModel::value($model, 'siteName', '');

        if (empty($siteName) && defined('CBSiteName')) { // @deprecated
            return CBSiteName;
        } else {
            return $siteName;
        }
    }

    /**
     * NOTE: 2017.03.19
     *
     *      Any code wanting the site URL should call this function to get it.
     *      Most of this time this function will return the value of the
     *      CBSiteURL constant. However, this constant should never be used
     *      directly. The implementation may change in the future.
     *
     *      A constant is currently used because if the user can edit this
     *      property and enters it incorrectly the website will stop working
     *      with very little recourse because the editor will no longer reload.
     *
     *      As other options become more reliable, or other constants provide
     *      more flexibility, this function's implementation will change.
     *
     * @return string
     *      Returns the site URL with no trailing slash.
     */
    static function siteURL() {
        if (defined(CBSiteURL)) {
            return CBSiteURL;
        } else if (defined('COLBY_SITE_URL')) { // @deprecated
            return COLBY_SITE_URL;
        } else {
            return (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['SERVER_NAME'];
        }
    }

    /**
     * @return stdClass
     */
    public static function setFrontPageIDForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @param $spec stdClass
     *
     * @return stdClass
     */
    static function specToModel(stdClass $spec) {
        $model = (object)[
            'className' => __CLASS__,
            'administratorEmails' => CBModel::value($spec, 'administratorEmails', [], function ($value) {
                return array_unique(preg_split(
                    '/[\s,]+/', $value, null, PREG_SPLIT_NO_EMPTY
                ));
            }),
            'classNamesForUserSettings' => CBModel::value($spec, 'classNamesForUserSettings', [], function ($value) {
                if (is_array($value)) {
                    return array_map('trim', $value);
                } else {
                    return [];
                }
            }),
            'siteName' => CBModel::value($spec, 'siteName', '', 'trim'),
        ];

        $model->debug = isset($spec->debug) ? !!$spec->debug : false;
        $model->defaultClassNameForPageSettings = isset($spec->defaultClassNameForPageSettings) ? trim($spec->defaultClassNameForPageSettings) : '';
        $model->disallowRobots = isset($spec->disallowRobots) ? !!$spec->disallowRobots : false;
        $model->facebookURL = CBModel::value($spec, 'facebookURL', '', 'trim');
        $model->frontPageID = CBModel::value($spec, 'frontPageID');
        $model->googleTagManagerID = isset($spec->googleTagManagerID) ? trim($spec->googleTagManagerID) : '';
        $model->onDemandImageResizeOperations = CBModel::value($spec, 'onDemandImageResizeOperations', '');
        $model->reCAPTCHASecretKey = CBModel::value($spec, 'reCAPTCHASecretKey', null, 'trim');
        $model->reCAPTCHASiteKey = CBModel::value($spec, 'reCAPTCHASiteKey', null, 'trim');
        $model->twitterURL = CBModel::value($spec, 'twitterURL', '', 'trim');

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
     * This function should be called by installation functions that want to
     * remove a class name for user settings.
     *
     * @param string $className
     *
     * @return null
     */
    static function uninstallClassNameForUserSettings($className) {
        $spec = CBModels::fetchSpecByID(CBSitePreferences::ID);

        // We assume spec has been saved by the CBSitePreferences::install()
        // function. If not, we want to let the error occur as notification.

        if (empty($spec->classNamesForUserSettings)) {
            return;
        } else {
            $classNames = array_values(array_filter($spec->classNamesForUserSettings, function ($value) use ($className) {
                return $value !== $className;
            }));

            if (count($classNames) !== count($spec->classNamesForUserSettings)) {
                $spec->classNamesForUserSettings = $classNames;

                CBModels::save([$spec]);
            }
        }
    }
}
