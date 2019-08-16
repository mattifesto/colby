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

    /* deprecated use CBSitePreferences::ID() */
    const ID = '89b64c9cab5a6c28cfbfe0d2c1c7f97e9821f452';

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

    private static $model = null;

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
    /* administratorEmails() */


    /**
     * @return [string]
     */
    static function CBAdmin_getIssueMessages(): array {
        $issueMessages = [];

        $message = CBSitePreferences::getIssueMessageForAck();

        if ($message !== null) {
            array_push(
                $issueMessages,
                $message
            );
        }

        return $issueMessages;
    }
    /* CBAdmin_getIssueMessages() */


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
        return [
            'CBModels'
        ];
    }
    /* CBInstall_requiredClassNames() */


    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(stdClass $spec): stdClass {
        $model = (object)[
            'absoluteAckPath' => trim(
                CBModel::valueToString($spec, 'absoluteAckPath')
            ),
            'custom' => CBKeyValuePair::valueToObject($spec, 'custom'),
            'debug' => CBModel::valueToBool($spec, 'debug'),
            'disallowRobots' => CBModel::valueToBool($spec, 'disallowRobots'),
            'facebookURL' => trim(
                CBModel::valueToString($spec, 'facebookURL')
            ),
            'frontPageID' => CBModel::valueAsID($spec, 'frontPageID'),
            'googleTagManagerID' => trim(
                CBModel::valueToString($spec, 'googleTagManagerID')
            ),
            'onDemandImageResizeOperations' => CBModel::valueToString(
                $spec,
                'onDemandImageResizeOperations'
            ),

            'path' => trim(
                CBModel::valueToString($spec, 'path')
            ),

            'reCAPTCHASecretKey' => trim(
                CBModel::valueToString($spec, 'reCAPTCHASecretKey')
            ),
            'reCAPTCHASiteKey' => trim(
                CBModel::valueToString($spec, 'reCAPTCHASiteKey')
            ),
            'siteName' => trim(
                CBModel::valueToString($spec, 'siteName')
            ),
            'slackWebhookURL' => trim(
                CBModel::valueToString($spec, 'slackWebhookURL')
            ),
            'twitterURL' => trim(
                CBModel::valueToString($spec, 'twitterURL')
            ),
        ];

        /* image for icon */

        $imageForIconSpec = CBModel::valueAsModel(
            $spec,
            'imageForIcon',
            ['CBImage']
        );

        if ($imageForIconSpec) {
            $model->imageForIcon = CBModel::build($imageForIconSpec);
        }

        /* administrator emails */

        $administatorEmails = CBModel::valueToString(
            $spec,
            'administratorEmails'
        );

        $administatorEmails = array_values(
            array_unique(
                preg_split(
                    '/[\s,]+/',
                    $administatorEmails,
                    null,
                    PREG_SPLIT_NO_EMPTY
                )
            )
        );

        $model->administratorEmails = $administatorEmails;

        /* done */

        return $model;
    }
    /* CBModel_build() */


    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_upgrade(stdClass $spec): stdClass {
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
    /* CBModel_upgrade() */


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
     * @return string|null
     */
    private static function getIssueMessageForAck(): ?string {
        $model = CBSitePreferences::model();

        $absoluteAckPath = CBModel::valueToString($model, 'absoluteAckPath');

        if (empty($absoluteAckPath)) {
            $ackPath = "ack";
        } else {
            $ackPath = $absoluteAckPath;
        }

        exec("{$ackPath} --version", $output, $returnStatus);

        if ($returnStatus == 0) {
            return null;
        }

        $editSitePreferencesLink = (
            '/admin/?c=CBModelEditor&ID=' .
            CBSitePreferences::ID()
        );

        if ($absoluteAckPath) {
            $absoluteAckPathAsMessage = CBMessageMarkup::stringToMessage(
                $absoluteAckPath
            );

            $message = <<<EOT

                Trying to execute the (ack (code)) command at the absolute
                ack path, ({$absoluteAckPathAsMessage} (code)), which is set
                in site preferences generated a status code of
                ({$returnStatus} (code)). Fix or remove the absolute ack
                path in (site preferences (a {$editSitePreferencesLink})).

EOT;
        } else {
            $message = <<<EOT

                Trying to execute the (ack (code)) command generated a
                status code of ({$returnStatus} (code)). Set the absolute
                ack path in (site preferences (a
                {$editSitePreferencesLink})).

EOT;
        }

        return $message;
    }
    /* getIssueMessageForAck() */


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
     * This function fetches the CBSitePreferences model from a file instead of
     * the database which has historically been how this model is intended to be
     * accessed.
     *
     * @NOTE 2018_09_01
     *
     *      I think this pattern was to allow site preferences to be available
     *      even if the database is down. Or maybe it was to reduce the database
     *      calls required per request since this model is requested every time.
     *
     *      Either way, add documentation here including scenarios for why this
     *      behavior is necessary. It actually may not be a good idea.
     *
     * @return model
     *
     *      The current CBSitePreferences model.
     */
    static function model() {
        if (empty(CBSitePreferences::$model)) {
            $filepath = CBDataStore::flexpath(
                CBSitePreferences::ID(),
                'site-preferences.json',
                cbsitedir()
            );

            if (is_file($filepath)) {
                $model = json_decode(
                    file_get_contents($filepath)
                );
            } else {
                $model = CBModel::build(
                    (object)[
                        'className' => 'CBSitePreferences',
                    ]
                );
            }

            CBSitePreferences::$model = $model;
        }

        return CBSitePreferences::$model;
    }
    /* model() */


    /**
     * This function saves the model to disk so that the database doesn't have
     * to be involved to use the information. Since this data is such core
     * system data this also avoids race conditions. For instance, if the
     * database is down we can still get the information needed to send an
     * alert email.
     *
     * @return void
     */
    static function CBModels_willSave(array $models): void {
        array_map(
            function ($model) {
                $filepath = CBDataStore::flexpath(
                    $model->ID,
                    'site-preferences.json',
                    cbsitedir()
                );

                CBDataStore::create($model->ID);

                file_put_contents($filepath, json_encode($model));
            },
            $models
        );

        /**
         * Clear the cached model.
         */
        CBSitePreferences::$model = null;
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

        $spec = CBModels::fetchSpecByID(CBSitePreferences::ID());
        $spec->frontPageID = $ID;

        CBModels::save($spec);
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
}
