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
    /* deprecated use CBSitePreferences::ID() */

    const defaultResizeOperations = [
        'rs200clc200',  /*  100 x 100 */
        'rw320',        /*  160 x ? */
        'rw480',        /*  240 x ? */
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
        $emails = CBModel::valueToArray($model, 'administratorEmails');

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
     * @param object $args
     *
     *      {
     *          ID: ID
     *      }
     *
     * @return object
     */
    static function CBAjax_setFrontPageID($args): stdClass {
        $ID = CBModel::valueAsID($args, 'ID');

        if (empty($ID)) {
            throw new InvalidArgumentException('No ID argument was provided.');
        }

        CBSitePreferences::setFrontPageID($ID);

        return (object)[
            'message' => 'The front page was changed successfully.',
        ];
    }


    /**
     * @return string
     */
    static function CBAjax_setFrontPageID_group(): string {
        return 'Administrators';
    }

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $originalSpec = CBModels::fetchSpecByID(CBSitePreferences::ID());

        if (empty($originalSpec)) {
            $spec = (object)[
                'ID' => CBSitePreferences::ID(),
            ];
        } else {
            $spec = CBModel::clone($originalSpec);
        }

        $spec->className = 'CBSitePreferences';

        unset($spec->classNamesForUserSettings);

        if ($spec != $originalSpec) {
            CBDB::transaction(function () use ($spec) {
                CBModels::save($spec);
            });
        }
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBModels'];
    }

    /**
     * @param model $spec
     *
     * @return model
     */
    static function CBModel_build(stdClass $spec) {
        $model = (object)[
            'className' => __CLASS__,
            'administratorEmails' => CBModel::value($spec, 'administratorEmails', [], function ($value) {
                return array_unique(preg_split(
                    '/[\s,]+/', $value, null, PREG_SPLIT_NO_EMPTY
                ));
            }),
            'imageForIcon' => CBModel::build(CBModel::valueAsModel($spec, 'imageForIcon', ['CBImage'])),
            'siteName' => trim(CBModel::valueToString($spec, 'siteName')),
            'slackWebhookURL' => trim(CBModel::valueToString($spec, 'slackWebhookURL')),
        ];

        $model->debug = isset($spec->debug) ? !!$spec->debug : false;
        $model->disallowRobots = isset($spec->disallowRobots) ? !!$spec->disallowRobots : false;
        $model->facebookURL = CBModel::value($spec, 'facebookURL', '', 'trim');
        $model->frontPageID = CBModel::value($spec, 'frontPageID');
        $model->googleTagManagerID = isset($spec->googleTagManagerID) ? trim($spec->googleTagManagerID) : '';
        $model->onDemandImageResizeOperations = CBModel::value($spec, 'onDemandImageResizeOperations', '');
        $model->reCAPTCHASecretKey = CBModel::value($spec, 'reCAPTCHASecretKey', null, 'trim');
        $model->reCAPTCHASiteKey = CBModel::value($spec, 'reCAPTCHASiteKey', null, 'trim');
        $model->twitterURL = CBModel::value($spec, 'twitterURL', '', 'trim');

        /* custom values */

        $model->custom = CBKeyValuePair::valueToObject($spec, 'custom');

        return $model;
    }

    /**
     * @param model $spec
     *
     * @return model
     */
    static function CBModel_upgrade(stdClass $spec) {
        /**
         * 2018.04.11 Remove unused property
         * Can be removed after run on every site
         */
        if (isset($spec->defaultClassNameForPageSettings)) {
            unset($spec->defaultClassNameForPageSettings);

            CBLog::log((object)[
                'className' => __CLASS__,
                'severity' => 5,
                'message' => <<<EOT

                    Removed the "defaultClassNameForPageSettings" property from
                    the CBSitePreferences spec because it is no longer used.

EOT
            ]);
        }

        return $spec;
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    static function customValueForKey($key) {
        $model = CBSitePreferences::model();

        return CBModel::value($model, "custom.{$key}");
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
     * @NOTE 2018.05.20
     *
     *      In some scenarios it may be better to check whether the current user
     *      is a member of the Developers group than to use this function. For
     *      instance, when deciding whether to display error stack traces on a
     *      web page, it may be more important to determine that the user can
     *      understand and use the information rather than if the site is in
     *      debug mode.
     *
     * @NOTE 2018.05.20
     *
     *      The ability to set debug mode on a website is very old. The actual
     *      number of practical purposes for it is low and may be zero. This
     *      property may be a candidate for deprecation. Its purpose should be
     *      more clearly determined.
     *
     * @return bool
     */
    static function debug() {
        $model = CBSitePreferences::model();

        return (bool)CBModel::value($model, 'debug');
    }

    /**
     * @return bool
     */
    static function disallowRobots() {
        $model = CBSitePreferences::model();

        return (bool)CBModel::value($model, 'disallowRobots');
    }

    /**
     * @return hex160?
     */
    static function frontPageID(): ?string {
        return CBModel::valueAsID(CBSitePreferences::model(), 'frontPageID');
    }

    /**
     * @return string
     *  Returns an empty string if unset for legacy reasons.
     */
    static function googleTagManagerID() {
        $model = CBSitePreferences::model();
        return empty($model->googleTagManagerID) ? '' : $model->googleTagManagerID;
    }

    /**
     * @return ID
     */
    static function ID(): string {
        return '89b64c9cab5a6c28cfbfe0d2c1c7f97e9821f452';
    }

    /**
     * @return object|false
     *
     *      The object is a CBImage model.
     */
    static function imageForIcon() {
        $model = CBSitePreferences::model();

        if (empty($model->imageForIcon)) {
            return false;
        } else {
            return $model->imageForIcon;
        }
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
                $model = CBModel::toModel((object)[
                    'className' => 'CBSitePreferences',
                ]);
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
    static function modelsWillSave(array $tuples) {
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
     * @return string
     */
    static function mysqlDatabase() {
        return defined('CBMySQLDatabase') ? CBMySQLDatabase : COLBY_MYSQL_DATABASE;
    }

    /**
     * @return string
     */
    static function mysqlHost() {
        return defined('CBMySQLHost') ? CBMySQLHost : COLBY_MYSQL_HOST;
    }

    /**
     * @return string
     */
    static function mysqlPassword() {
        return defined('CBMySQLPassword') ? CBMySQLPassword : COLBY_MYSQL_PASSWORD;
    }

    /**
     * @return string
     */
    static function mysqlUser() {
        return defined('CBMySQLUser') ? CBMySQLUser : COLBY_MYSQL_USER;
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
    static function reCAPTCHASecretKey() {
        $model = CBSitePreferences::model();
        return empty($model->reCAPTCHASecretKey) ? null : $model->reCAPTCHASecretKey;
    }

    /**
     * @return string|null
     */
    static function reCAPTCHASiteKey() {
        $model = CBSitePreferences::model();
        return empty($model->reCAPTCHASiteKey) ? null : $model->reCAPTCHASiteKey;
    }

    /**
     * @deprecated Changing the default to true for now because I can't think
     * of a situation where we don't want error reports. This function should
     * not be used and if a reason is found for it document it well here.
     *
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

        return true;
    }

    /**
     * @param ID $ID
     *
     * @return void
     */
    static function setFrontPageID(string $ID): void {
        if (!CBHex160::is($ID)) {
            throw new InvalidArgumentException("'{$ID}' is not a valid ID");
        }

        $spec = CBModels::fetchSpecByID(CBSitePreferences::ID);
        $spec->frontPageID = $ID;
        CBModels::save([$spec]);

        /* clear cache */
        CBSitePreferences::$model = false;
    }

    /**
     * @deprecated use cbsitedir()
     */
    static function siteDirectory() {
        return cbsitedir();
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

        return CBModel::valueToString($model, 'siteName');
    }

    /**
     * @deprecated use cbsiteurl()
     */
    static function siteURL() {
        return cbsiteurl();
    }
}
