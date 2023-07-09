<?php

/**
 * This class provides access to the most important site preferences. As such
 * the functions in this class return default values even if the site hasn't
 * been fully or properly installed or updated yet.
 *
 * Developers should call the functions on this class rather than accessing the
 * model directly so that the use of deprecated properties can be found and the
 * implementation of individual property calculation can change.
 */
final class
CBSitePreferences {

    /* deprecated use CBSitePreferences::ID() */
    const ID = '89b64c9cab5a6c28cfbfe0d2c1c7f97e9821f452';

    private const
    defaultResizeOperations =
    [
        'rl320',        /* long edge of 160pt */
        'rl640',        /* long edge of 320pt */
        'rl960',        /* long edge of 480pt */
        'rl1280',       /* long edge of 640pt */
        'rl1600',       /* long edge of 800pt */
        'rl1920',       /* long edge of 960pt */
        'rl2560',       /* long edge of 1280pt */

        'rs200clc200',  /*  100pt x 100pt */
        'rs320clc320',  /*  160pt x 160pt */

        'rw320',        /*  160pt x ? */
        'rw480',        /*  240pt x ? */
        'rw640',        /*  320pt x ? */
        'rw960',        /*  480pt x ? */
        'rw1280',       /*  640pt x ? */
        'rw1600',       /*  800pt x ? */
        'rw1920',       /*  960pt x ? */
        'rw2560',       /* 1280pt x ? */
        'rw3200',       /* 1600pt x ? */
        'rw3840',       /* 1920pt x ? */
        'rw4480',       /* 2240pt x ? */
        'rw5120',       /* 2560pt x ? */

        /**
         * @NOTE 2022_10_05_1664989774
         *
         *      This operation was added to support the CB_View_Moment2 class.
         *      We probaby need a way to register resize operations.
         */
        'rh800rw2560',
    ];

    private static $model = null;



    // -- CBCodeAdmin interfaces



    /**
     * @return object
     */
    static function
    CBCodeAdmin_searches(
    ): array
    {
        $arrayOfCBCodeSearchSpecs =
        [];



        // CBSitePreferences::getYouTubeAPIKey()

        $codeSearchSpec =
        CBModel::createSpec(
            'CBCodeSearch',
            '3fa3838507d648fa1866253bd55784911d0d352e'
        );

        CBCodeSearch::setNoticeVersion(
            $codeSearchSpec,
            '2022_06_28_1656427937'
        );

        CBCodeSearch::setWarningVersion(
            $codeSearchSpec,
            '2022_06_28_1656427938'
        );

        $codeSearchSpec->cbmessage =
        <<<EOT

            Use a CB_YouTubeChannel model.

        EOT;

        $codeSearchSpec->regex =
        '\bCBSitePreferences::getYouTubeAPIKey\b';

        $codeSearchSpec->severity =
        4;

        $codeSearchSpec->title =
        'CBSitePreferences::getYouTubeAPIKey()';

        array_push(
            $arrayOfCBCodeSearchSpecs,
            $codeSearchSpec
        );



        // CBSitePreferences::getYouTubeChannelID()

        $codeSearchSpec =
        CBModel::createSpec(
            'CBCodeSearch',
            '02662ab106aed59262e0f87fa714d380220201f0'
        );

        CBCodeSearch::setNoticeVersion(
            $codeSearchSpec,
            '2022_06_28_1656427678'
        );

        CBCodeSearch::setWarningVersion(
            $codeSearchSpec,
            '2022_06_28_1656427679'
        );

        $codeSearchSpec->cbmessage =
        <<<EOT

            Use a CB_YouTubeChannel model.

        EOT;

        $codeSearchSpec->regex =
        '\bCBSitePreferences::getYouTubeChannelID\b';

        $codeSearchSpec->severity =
        4;

        $codeSearchSpec->title =
        'CBSitePreferences::getYouTubeChannelID()';

        array_push(
            $arrayOfCBCodeSearchSpecs,
            $codeSearchSpec
        );



        // CBSitePreferences_youtubeAPIKey

        $codeSearchSpec =
        CBModel::createSpec(
            'CBCodeSearch',
            'a43c9470bb1e27f393c6f3c7bd06725aa6770395'
        );

        CBCodeSearch::setNoticeVersion(
            $codeSearchSpec,
            '2022_06_28_1656428227'
        );

        CBCodeSearch::setWarningVersion(
            $codeSearchSpec,
            '2022_06_28_1656428228'
        );

        $codeSearchSpec->cbmessage =
        <<<EOT

            Use a CB_YouTubeChannel model.

        EOT;

        $codeSearchSpec->regex =
        '\bCBSitePreferences_youtubeAPIKey\b';

        $codeSearchSpec->severity =
        4;

        $codeSearchSpec->title =
        'CBSitePreferences_youtubeAPIKey';

        array_push(
            $arrayOfCBCodeSearchSpecs,
            $codeSearchSpec
        );



        // CBSitePreferences_youtubeChannelID

        $codeSearchSpec =
        CBModel::createSpec(
            'CBCodeSearch',
            '5dd1354973659c53aab45f7199c520441396ac97'
        );

        CBCodeSearch::setNoticeVersion(
            $codeSearchSpec,
            '2022_06_28_1656428356'
        );

        CBCodeSearch::setWarningVersion(
            $codeSearchSpec,
            '2022_06_28_1656428357'
        );

        $codeSearchSpec->cbmessage =
        <<<EOT

            Use a CB_YouTubeChannel model.

        EOT;

        $codeSearchSpec->regex =
        '\bCBSitePreferences_youtubeChannelID\b';

        $codeSearchSpec->severity =
        4;

        $codeSearchSpec->title =
        'CBSitePreferences_youtubeChannelID';

        array_push(
            $arrayOfCBCodeSearchSpecs,
            $codeSearchSpec
        );



        // CBSitePreferences_headerImage_property

        $codeSearchSpec =
        CBModel::createSpec(
            'CBCodeSearch',
            '503e1a636261e6fe0cb893459efc9d7368730e77'
        );

        CBCodeSearch::setAckArguments(
            $codeSearchSpec,
            '--ignore-file=is:CBSitePreferences.php'
        );

        CBCodeSearch::setCBMessage(
            $codeSearchSpec,
            <<<EOT

                The header image property is no longer supported.

            EOT
        );

        CBCodeSearch::setNoticeVersion(
            $codeSearchSpec,
            '2022_10_08_1665245227'
        );

        CBCodeSearch::setWarningVersion(
            $codeSearchSpec,
            '2022_10_08_1665245228'
        );

        CBCodeSearch::setErrorVersion(
            $codeSearchSpec,
            '2022_10_08_1665245229'
        );

        $codeSearchSpec->regex =
        '\bCBSitePreferences_headerImage_property\b';

        $codeSearchSpec->severity =
        3;

        $codeSearchSpec->title =
        'CBSitePreferences_headerImage_property';

        array_push(
            $arrayOfCBCodeSearchSpecs,
            $codeSearchSpec
        );



        // CBSitePreferences::getHeaderImage()
        // CBSitePreferences::setHeaderImage()

        $codeSearchSpec =
        CBModel::createSpec(
            'CBCodeSearch',
            'e5e67453934a52a9d7886cc7dc9556b3fabd1cdc'
        );

        CBCodeSearch::setAckArguments(
            $codeSearchSpec,
            '--ignore-file=is:CBSitePreferences.php'
        );

        CBCodeSearch::setCBMessage(
            $codeSearchSpec,
            <<<EOT

                CBSitePreferences::getHeaderImage() and
                CBSitePreferences::setHeaderImage() are no longer available.

            EOT
        );

        CBCodeSearch::setNoticeVersion(
            $codeSearchSpec,
            '2022_10_08_1665249339'
        );

        CBCodeSearch::setWarningVersion(
            $codeSearchSpec,
            '2022_10_08_1665249340'
        );

        CBCodeSearch::setErrorVersion(
            $codeSearchSpec,
            '2022_10_08_1665249341'
        );

        $codeSearchSpec->regex =
        '\bCBSitePreferences::getHeaderImage\b|' .
        '\bCBSitePreferences::setHeaderImage\b';

        $codeSearchSpec->severity =
        3;

        $codeSearchSpec->title =
        CBConvert::stringToCleanLine(<<<EOT

            CBSitePreferences::getHeaderImage(),
            CBSitePreferences::setHeaderImage()

        EOT);

        array_push(
            $arrayOfCBCodeSearchSpecs,
            $codeSearchSpec
        );



        // done

        return $arrayOfCBCodeSearchSpecs;
    }
    // CBCodeAdmin_searches()



    // -- functions



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
        $functionNames = [
            'getIssueMessage_ack',
            'getIssueMessage_mysqldump',
            'getIssueMessage_siteName',
            'getIssueMessage_slackWebhookURL',
        ];

        $issueMessages = [];

        foreach ($functionNames as $functionName) {
            $functionName = "CBSitePreferences::{$functionName}";

            $message = call_user_func($functionName);

            if ($message !== null) {
                array_push(
                    $issueMessages,
                    $message
                );
            }
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
    /* CBAjax_setFrontPageID() */



    /**
     * @return string
     */
    static function CBAjax_setFrontPageID_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
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
    static function
    CBModel_build(
        stdClass $spec
    ): stdClass {
        $model = (object)[
            'custom' => CBKeyValuePair::valueToObject($spec, 'custom'),
            'disallowRobots' => CBModel::valueToBool($spec, 'disallowRobots'),
            'facebookURL' => trim(
                CBModel::valueToString($spec, 'facebookURL')
            ),
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



        // icon image

        $iconImageSpec =
        CBSitePreferences::getIconImage(
            $spec
        );

        if (
            $iconImageSpec !== null
        ) {
            $iconImageModel =
            CBModel::build(
                $iconImageSpec
            );

            CBSitePreferences::setIconImage(
                $model,
                $iconImageModel
            );
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

        CBSitePreferences::setAdSensePublisherID(
            $model,
            CBSitePreferences::getAdSensePublisherID(
                $spec
            )
        );

        CBSitePreferences::setAdsTxtContent(
            $model,
            CBSitePreferences::getAdsTxtContent(
                $spec
            )
        );

        CBSitePreferences::setAppearance(
            $model,
            CBSitePreferences::getAppearance(
                $spec
            )
        );

        CBSitePreferences::setEnvironment(
            $model,
            CBSitePreferences::getEnvironment(
                $spec
            )
        );

        CBSitePreferences::setFrontPageModelCBID(
            $model,
            CBSitePreferences::getFrontPageModelCBID(
                $spec
            )
        );

        CBSitePreferences::setYouTubeChannelID(
            $model,
            CBSitePreferences::getYouTubeChannelID(
                $spec
            )
        );

        CBSitePreferences::setYouTubeAPIKey(
            $model,
            CBSitePreferences::getYouTubeAPIKey(
                $spec
            )
        );


        /* done */

        return $model;
    }
    /* CBModel_build() */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_upgrade(
        stdClass $spec
    ): stdClass {

        /**
         * 2020_12_19
         *
         *      The last use of this property was removed in version 675 so this
         *      code can be removed in version 677.
         */
        if (isset($spec->defaultClassNameForPageSettings)) {
            unset($spec->defaultClassNameForPageSettings);

            CBLog::log(
                (object)[
                    'className' => __CLASS__,
                    'severity' => 5,
                    'message' => <<<EOT

                        Removed the "defaultClassNameForPageSettings" property
                        from the CBSitePreferences spec because it is no longer
                        used.

                    EOT,
                ]
            );
        }

        return $spec;
    }
    /* CBModel_upgrade() */



    /* -- accessors -- */



    /**
     * @param object $sitePreferencesModel
     *
     * @return string
     *
     *      Returns an empty string if the property value has not been set.
     */
    static function
    getAdSensePublisherID(
        stdClass $sitePreferencesModel
    ): string {
        return trim(
            CBModel::valueToString(
                $sitePreferencesModel,
                'CBSitePreferences_adSensePublisherID'
            )
        );
    }
    /* getAdSensePublisherID() */



    /**
     * @param object $sitePreferencesModel
     * @param string $newAdSensePublisherID
     *
     * @return void
     */
    static function
    setAdSensePublisherID(
        stdClass $sitePreferencesModel,
        string $newAdSensePublisherID
    ): void {
        $sitePreferencesModel->CBSitePreferences_adSensePublisherID = (
            $newAdSensePublisherID
        );
    }
    /* setAdSensePublisherID() */



    /**
     * @param object $sitePreferencesModel
     *
     * @return string
     *
     *      Returns an empty string if the property value has not been set.
     */
    static function
    getAdsTxtContent(
        stdClass $sitePreferencesModel
    ): string {
        return CBModel::valueToString(
            $sitePreferencesModel,
            'CBSitePreferences_adsTxtContent'
        );
    }
    /* getAdsTxtContent() */



    /**
     * @param object $sitePreferencesModel
     * @param string $newAdsTxtContent
     *
     * @return void
     */
    static function
    setAdsTxtContent(
        stdClass $sitePreferencesModel,
        string $newAdsTxtContent
    ): void {
        $sitePreferencesModel->CBSitePreferences_adsTxtContent = (
            $newAdsTxtContent
        );
    }
    /* setAdsTxtContent() */



    /**
     * @param object $sitePreferencesModel
     *
     * @return string
     *
     *      Returns an empty string if the website appearance automatically
     *      matches the user's computer preference.
     */
    static function
    getAppearance(
        stdClass $sitePreferencesModel
    ): string {
        $appearance = trim(
            CBModel::valueToString(
                $sitePreferencesModel,
                'CBSitePreferences_appearance'
            )
        );

        if (
            in_array(
                $appearance,
                CBSitePreferences::getAppearanceOptions()
            )
        ) {
            return $appearance;
        } else {
            return 'CBSitePreferences_appearance_light';
        }
    }
    /* getAppearance() */



    /**
     * @param object $sitePreferencesModel
     * @param string $newAppearance
     *
     * @return void
     */
    static function
    setAppearance(
        stdClass $sitePreferencesModel,
        string $newAppearance
    ): void {
        if (
            !in_array(
                $newAppearance,
                CBSitePreferences::getAppearanceOptions()
            )
        ) {
            $newAppearance = 'CBSitePreferences_appearance_light';
        }

        $sitePreferencesModel->CBSitePreferences_appearance = $newAppearance;
    }
    /* setAppearance() */



    /**
     * @param object $sitePreferencesModel
     *
     * @return string
     *
     *      Returns an empty string if the website environment is unknown.
     */
    static function
    getEnvironment(
        stdClass $sitePreferencesModel
    ): string {
        $environment = trim(
            CBModel::valueToString(
                $sitePreferencesModel,
                'CBSitePreferences_environment'
            )
        );

        if (
            in_array(
                $environment,
                CBSitePreferences::getEnvironmentOptions()
            )
        ) {
            return $environment;
        } else {
            return '';
        }
    }
    /* getEnvironment() */



    /**
     * @param object $sitePreferencesModel
     * @param string $newEnvironment
     *
     * @return void
     */
    static function
    setEnvironment(
        stdClass $sitePreferencesModel,
        string $newEnvironment
    ): void {
        if (
            !in_array(
                $newEnvironment,
                CBSitePreferences::getEnvironmentOptions()
            )
        ) {
            $newEnvironment = '';
        }

        $sitePreferencesModel->CBSitePreferences_environment = $newEnvironment;
    }
    /* setEnvironment() */



    /**
     * @param object $sitePreferencesModel
     *
     * @return string
     *
     *      Returns an empty string if the property value has not been set.
     */
    static function
    getFrontPageModelCBID(
        stdClass $sitePreferencesModel
    ): ?string {
        return CBModel::valueAsCBID(
            $sitePreferencesModel,
            'frontPageID'
        );
    }
    /* getFrontPageModelCBID() */



    /**
     * @param object $sitePreferencesModel
     * @param string $frontPageModelCBID
     *
     * @return void
     */
    static function
    setFrontPageModelCBID(
        stdClass $sitePreferencesModel,
        ?string $frontPageModelCBID
    ): void {
        if (
            $frontPageModelCBID !== null &&
            !CBID::valueIsCBID($frontPageModelCBID)
        ) {
            throw new CBExceptionWithValue(
                'The front page model CBID argument is not valid.',
                (object)[
                    'targetModel' => $sitePreferencesModel,
                    'frontPageModelCBID' => $frontPageModelCBID,
                ],
                '2ca8a3dfa005d9247df9178ad21244ebbcf237d2'
            );
        }

        $sitePreferencesModel->frontPageID = $frontPageModelCBID;
    }
    /* setFrontPageModelCBID() */



    /**
     * @param object $sitePreferencesModel
     *
     * @return object|null
     */
    static function
    getIconImage(
        stdClass $sitePreferencesModel
    ): ?stdClass
    {
        $iconImageModel =
        CBModel::valueAsModel(
            $sitePreferencesModel,
            'imageForIcon',
            'CBImage'
        );

        return $iconImageModel;
    }
    // getIconImage()



    /**
     * @param object $sitePreferencesModel
     * @param object $iconImageModel
     *
     * @return void
     */
    static function
    setIconImage(
        stdClass $sitePreferencesModel,
        ?stdClass $iconImageModel
    ): void {
        $sitePreferencesModel->imageForIcon =
        $iconImageModel;
    }
    // setIconImage()



    /**
     * @param object $sitePreferencesModel
     *
     * @return string
     *
     *      Returns an empty string if the property value has not been set.
     */
    static function
    getYouTubeAPIKey(
        stdClass $sitePreferencesModel
    ): string {
        return trim(
            CBModel::valueToString(
                $sitePreferencesModel,
                'CBSitePreferences_youtubeAPIKey'
            )
        );
    }
    /* getYouTubeAPIKey() */



    /**
     * @param object $sitePreferencesModel
     * @param string $youtubeAPIKey
     *
     * @return void
     */
    static function
    setYouTubeAPIKey(
        stdClass $sitePreferencesModel,
        string $youtubeAPIKey
    ): void {
        $sitePreferencesModel->CBSitePreferences_youtubeAPIKey = $youtubeAPIKey;
    }
    /* setYouTubeAPIKey() */



    /**
     * @param object $sitePreferencesModel
     *
     * @return string
     *
     *      Returns an empty string if the property value has not been set.
     */
    static function
    getYouTubeChannelID(
        stdClass $sitePreferencesModel
    ): string {
        return trim(
            CBModel::valueToString(
                $sitePreferencesModel,
                'CBSitePreferences_youtubeChannelID'
            )
        );
    }
    /* getYouTubeChannelID() */



    /**
     * @param object $sitePreferencesModel
     * @param string $youtubeChannelID
     *
     * @return void
     */
    static function
    setYouTubeChannelID(
        stdClass $sitePreferencesModel,
        string $youtubeChannelID
    ): void {
        $sitePreferencesModel->CBSitePreferences_youtubeChannelID = (
            $youtubeChannelID
        );
    }
    /* setYouTubeChannelID() */



    /* -- functions -- */



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
     * @deprecated use CBSitePreferences::getEnvironment()
     */
    static function
    debug(
    ) {
        return CBSitePreferences::getIsDevelopmentWebsite();
    }
    /* debug() */



    /**
     * @return bool
     */
    static function disallowRobots() {
        return CBModel::valueToBool(
            CBSitePreferences::model(),
            'disallowRobots'
        );
    }
    /* disallowRobots() */



    /**
     * @TODO 2022_01_16
     *
     *      Deprecate this function and use the accessors.
     *
     * @return CBID|null
     */
    static function
    frontPageID(
    ): ?string {
        return CBSitePreferences::getFrontPageModelCBID(
            CBSitePreferences::model()
        );
    }
    /* frontPageID() */



    /**
     * @return [string]
     */
    static function
    getAppearanceOptions(
    ): array {
        return [
            'CBSitePreferences_appearance_light',
            'CBSitePreferences_appearance_dark',
            'CBSitePreferences_appearance_auto',
        ];
    }
    /* getAppearanceOptions() */



    /**
     * @deprecated 2021_11_06
     *
     *      Use CBSitePreferences::getEnvironment()
     *
     * Returns a boolean value indicating whether the site is a development site
     * (not a production site). This property is used to determine whether to
     * show development site only options such a "pull Colby".
     *
     * It is also used to show development only features and notifications.
     * That aren't applicable to even developers on production sites.
     *
     * A site should not be less secure because this property returns true. For
     * instance, a site shouldn't send passwords to debug log or reveal user
     * data to public pages.
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
     * @return bool
     */
    static function
    getIsDevelopmentWebsite(
    ): bool {
        $environment = CBSitePreferences::getEnvironment(
            CBSitePreferences::model()
        );

        return (
            $environment === 'CBSitePreferences_environment_development'
        );
    }
    /* getIsDevelopmentWebsite() */



    /**
     * @return [string]
     */
    static function
    getEnvironmentOptions(
    ): array {
        return [
            'CBSitePreferences_environment_development',
            'CBSitePreferences_environment_testing',
            'CBSitePreferences_environment_staging',
            'CBSitePreferences_environment_production',
        ];
    }
    /* getEnvironmentOptions() */



    /**
     * @return string|null
     */
    private static function getIssueMessage_ack(): ?string {
        $model = CBSitePreferences::model();

        exec("ack --version", $output, $returnStatus);

        if ($returnStatus == 0) {
            return null;
        }

        $editSitePreferencesLink = (
            '/admin/?c=CBModelEditor&ID=' .
            CBSitePreferences::ID()
        );

        $message = <<<EOT

            Trying to execute the (ack (code)) command generated a status code
            of ({$returnStatus} (code)). Update the path in (site preferences (a
            {$editSitePreferencesLink})).

        EOT;

        return $message;
    }
    /* getIssueMessage_ack() */


    /**
     * @return string|null
     */
    private static function getIssueMessage_mysqldump(): ?string {
        $model = CBSitePreferences::model();

        exec("mysqldump --version", $output, $returnStatus);

        if ($returnStatus == 0) {
            return null;
        }

        $editSitePreferencesLink = (
            '/admin/?c=CBModelEditor&ID=' .
            CBSitePreferences::ID()
        );

        $message = <<<EOT

            Trying to execute the (mysqldump (code)) command generated a status
            code of ({$returnStatus} (code)). Update the path in (site
            preferences (a {$editSitePreferencesLink})).

        EOT;

        return $message;
    }
    /* getIssueMessage_mysqldump() */


    /**
     * @return string|null
     */
    private static function getIssueMessage_siteName(): ?string {
        $siteName = CBModel::valueToString(
            CBSitePreferences::model(),
            'siteName'
        );

        if ($siteName !== '') {
            return null;
        }

        $editSitePreferencesLink = (
            '/admin/?c=CBModelEditor&ID=' .
            CBSitePreferences::ID()
        );

        $message = <<<EOT

            This site has no name. Set one in (site preferences (a
            {$editSitePreferencesLink})).

        EOT;

        return $message;
    }


    /**
     * @return string|null
     */
    private static function getIssueMessage_slackWebhookURL(): ?string {
        $siteName = CBModel::valueToString(
            CBSitePreferences::model(),
            'slackWebhookURL'
        );

        if ($siteName !== '') {
            return null;
        }

        $editSitePreferencesLink = (
            '/admin/?c=CBModelEditor&ID=' .
            CBSitePreferences::ID()
        );

        $message = <<<EOT

            This site has no Slack webhook URL. Set one in (site preferences (a
            {$editSitePreferencesLink})).

        EOT;

        return $message;
    }



    /**
     * @return string
     *
     *      Returns an empty string if unset.
     */
    static function googleTagManagerID(
    ): string {
        $model = CBSitePreferences::model();

        return CBModel::valueToString(
            $model,
            'googleTagManagerID'
        );
    }
    /* googleTagManagerID() */



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
    static function
    imageForIcon(
    ) {
        $model = CBSitePreferences::model();

        if (empty($model->imageForIcon)) {
            return false;
        } else {
            return $model->imageForIcon;
        }
    }
    /* imageForIcon() */



    /**
     * @NOTE 2019_08_15
     *
     *      The putenv() and getenv() functions are very odd in the following
     *      ways:
     *
     *          If you call putenv() to set PATH and getenv() to get it, you
     *          will not get the value you set with putenv().
     *
     *          However, if you call putenv() to set PATH, the path you set will
     *          be used by exec(). So you can call "exec('echo $PATH');" to get
     *          the path.
     *
     *          If you call apache_setenv() to set PATH and getenv() to get it,
     *          you will get the path you set with apache_setenv(), but exec()
     *          will not use this path.
     *
     *      So, we use putenv() to set PATH below because our goal is to set the
     *      path to be used by exec().
     *
     * @return void
     */
    static function
    initialize(
    ): void {
        $path = CBModel::valueToString(
            CBSitePreferences::model(),
            'path'
        );

        if ($path === '') {
            return;
        }

        $newpath = (
            getenv('PATH') .
            ':' .
            $path
        );

        putenv(
            "PATH={$newpath}"
        );
    }
    /* initialize() */



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
     * @TODO 2021_08_24
     *
     *      Non-standard code slows us down every time. This code is bad. If the
     *      database is down that's a red hot bug that needs to be fixed and
     *      there isn't likely much to be done until it is. This function should
     *      be replaced with CBModelCache::fetchModelByID().
     *
     * @return object
     *
     *      The current CBSitePreferences model.
     */
    static function
    model(
    ): stdClass {
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
     * @NOTE 2021_08_24
     *
     *      This code will eventually be removed. I'm not sure what "race
     *      conditions" the above comment is referring to and if sending emails
     *      without a database becomes important, which it has not been, it
     *      should be implemented specifically with tests.
     *
     * @return void
     */
    static function
    CBModels_willSave(
        array $models
    ): void {
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
    /* CBModels_willSave() */



    /**
     * @return string
     */
    static function
    mysqlDatabase(
    ): string {
        if (defined('CBMySQLDatabase')) {
            return CBMySQLDatabase;
        }

        if (defined('COLBY_MYSQL_DATABASE')) {
            return COLBY_MYSQL_DATABASE;
        }

        $configurationSpec = CB_Configuration::fetchConfigurationSpec();

        return CB_Configuration::getDatabaseName(
            $configurationSpec
        );
    }
    /* mysqlDatabase() */



    /**
     * @NOTE 2020_01_22
     *
     *      This function has been altered to return null if there is no MySQL
     *      host set, which is the case for a site that is in setup mode.
     *
     *      This may not be the best way to express this for the future, so work
     *      through the scenarios in the future.
     *
     * @return string|null
     */
    static function
    mysqlHost(
    ): ?string
    {
        $databaseHost = '';
        $configurationSpec = CB_Configuration::fetchConfigurationSpec();

        if ($configurationSpec !== null) {
            $databaseHost = CB_Configuration::getDatabaseHost(
                $configurationSpec
            );
        }

        $host =
        getenv(
            'MYSQL_HOST'
        );

        if (
            $host !==
            false
        ) {
            return $host;
        }


        if (defined('CBMySQLHost')) {
            return CBMySQLHost;
        } else if (defined('COLBY_MYSQL_HOST')) {
            return COLBY_MYSQL_HOST;
        } else if ($databaseHost !== '') {
            return $databaseHost;
        } else {
            return null;
        }
    }
    /* mysqlHost() */



    /**
     * @return string
     */
    static function
    mysqlPassword(
    ): string {
        if (defined('CBMySQLPassword')) {
            return CBMySQLPassword;
        }

        if (defined('COLBY_MYSQL_PASSWORD')) {
            return COLBY_MYSQL_PASSWORD;
        }


        $configurationSpec = CB_Configuration::fetchConfigurationSpec();

        return CB_Configuration::getDatabasePassword(
            $configurationSpec
        );
    }
    /* mysqlPassword() */



    /**
     * @return string
     */
    static function
    mysqlUser(
    ): string {
        if (defined('CBMySQLUser')) {
            return CBMySQLUser;
        }

        if (defined('COLBY_MYSQL_USER')) {
            return COLBY_MYSQL_USER;
        }

        $configurationSpec = CB_Configuration::fetchConfigurationSpec();

        return CB_Configuration::getDatabaseUsername(
            $configurationSpec
        );
    }
    /* mysqlUser() */



    /**
     * @return [string]
     *
     *      An array of image resize operations that are allowed to be completed
     *      when the specific image size URL is first requested.
     */
    static function
    onDemandImageResizeOperations(
    ) {
        $model = CBSitePreferences::model();

        $operations = CBModel::value(
            $model,
            'onDemandImageResizeOperations',
            [],
            function (
                $value
            ) {
                $operations = preg_split(
                    '/[\s,]+/',
                    $value,
                    -1,
                    PREG_SPLIT_NO_EMPTY
                );

                return (
                    is_array($operations) ?
                    $operations :
                    []
                );
            }
        );

        return array_unique(
            array_merge(
                $operations,
                CBSitePreferences::defaultResizeOperations
            )
        );
    }
    /* onDemandImageResizeOperations() */



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
    /* sendEmailsForErrors() */



    /**
     * @TODO 2022_01_16
     *
     *      Deprecate this function and use the accessors and save just like you
     *      would any other model.
     *
     * @param CBID $frontPageModelCBID
     * @param CBID $testSitePreferencesModelCBID
     *
     *      This parameter is only used by testing.
     *
     * @return void
     */
    static function
    setFrontPageID(
        string $frontPageModelCBID,
        string $testSitePreferencesModelCBID = null
    ): void {
        if (
            $testSitePreferencesModelCBID !== null
        ) {
            $sitePreferencesModelCBID = $testSitePreferencesModelCBID;
        } else {
            $sitePreferencesModelCBID = CBSitePreferences::ID();
        }

        $sitePreferencesSpec = CBModels::fetchSpecByID(
            $sitePreferencesModelCBID
        );

        CBSitePreferences::setFrontPageModelCBID(
            $sitePreferencesSpec,
            $frontPageModelCBID
        );

        CBDB::transaction(
            function () use (
                $sitePreferencesSpec
            ) {
                CBModels::save(
                    $sitePreferencesSpec
                );
            }
        );
    }
    /* setFrontPageID() */



    /**
     * @deprecated use cbsitedir()
     */
    static function
    siteDirectory(
    ) {
        return cbsitedir();
    }
    /* siteDirectory() */



    /**
     * @deprecated use CBConfiguration::primaryDomain();
     *
     * @return string
     */
    static function
    siteDomainName(
    ): string {
        return CBConfiguration::primaryDomain();
    }
    /* siteDomainName() */



    /**
     * @return string
     */
    static function siteName() {
        $model = CBSitePreferences::model();

        return CBModel::valueToString($model, 'siteName');
    }
}
