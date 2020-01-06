<?php

/**
 * This class is used to help render the /admin/ page.
 */
final class CBStatusAdminPage {

    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return string
     */
    static function CBAdmin_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /**
     * @param string $pageStub
     *
     * @return [string]
     */
    static function CBAdmin_menuNamePath(string $pageStub): array {
        return [
            'general',
            'status'
        ];
    }
    /* CBAdmin_menuNamePath() */



    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Website Status';

        $siteNameAsMessage = CBMessageMarkup::stringToMessage(
            CBSitePreferences::siteName()
        );

        $siteTypeAsMessage = (
            CBSitePreferences::getIsDevelopmentWebsite() ?
            'Development' :
            'Production'
        );

        $message = <<<EOT

            --- CBUI_sectionContainer
                --- CBUI_section
                    --- CBUI_container_leftAndRight
                        --- p CBUI_textColor2
                        Site Name
                        ---
                        {$siteNameAsMessage}
                    ---
                    --- CBUI_container_leftAndRight
                        --- p CBUI_textColor2
                        Site Type
                        ---
                        {$siteTypeAsMessage}
                    ---
                ---
            ---

            --- CBUI_title1
            Versions
            ---
        EOT;

        echo CBMessageMarkup::messageToHTML($message);


        /* status widgets */

        $statusWidgetFilepaths = Colby::globFiles(
            'classes/CBStatusWidgetFor*'
        );

        $statusWidgetClassNames = array_map(
            function ($filepath) {
                return basename($filepath, '.php');
            },
            $statusWidgetFilepaths
        );

        sort($statusWidgetClassNames);

        $statusWidgetMessage = '';

        foreach ($statusWidgetClassNames as $statusWidgetClassName) {
            $functionName = "{$statusWidgetClassName}::CBStatusAdminPage_data";

            if (is_callable($functionName)) {
                $data = call_user_func($functionName);
                $keyAsMessage = CBMessageMarkup::stringToMessage($data[0]);
                $valueAsMessage = CBMessageMarkup::stringToMessage($data[2]);

                $statusWidgetMessage .= <<<EOT

                    --- CBUI_container_leftAndRight
                        --- p CBUI_textColor2
                        {$keyAsMessage}
                        ---

                        {$valueAsMessage}
                    ---

                EOT;
            }
        }

        if ($statusWidgetMessage !== '') {
            $statusWidgetMessage = <<<EOT

                --- CBUI_sectionContainer
                    --- CBUI_section
                        {$statusWidgetMessage}
                    ---
                ---

            EOT;

            echo CBMessageMarkup::messageToHTML($statusWidgetMessage);
        }
    }
    /* CBAdmin_render() */



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v565.js', cbsysurl()),
        ];
    }



    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        $issues = [];
        $duplicateURIMessages = CBStatusAdminPage::fetchDuplicateURIMessages();

        if (
            CBUserGroup::currentUserIsMemberOfUserGroup(
                'CBDevelopersUserGroup'
            )
        ) {
            $deprecatedConstants = [
                ['CBSiteIsBeingDebugged', 'Use site preferences.'],
                ['CBSiteIsBeingDubugged', 'Use site preferences.'],
                ['COLBY_SITE_IS_BEING_DEBUGGED', 'Use site preferences.'],
                ['CBShouldDisallowRobots', 'Use site preferences.'],
                ['CBGoogleAnalyticsID', 'Use site preferences'],

                [
                    'GOOGLE_UNIVERSAL_ANALYTICS_TRACKING_ID',
                    'Use site preferences'
                ],

                [
                    'CBSiteConfiguration::defaultClassNameForPageSettings',
                    'Use site preferences.'
                ],

                ['CBFacebookFirstVerifiedUserID', 'Remove it.'],
                ['COLBY_FACEBOOK_FIRST_VERIFIED_USER_ID', 'Remove it.'],

                [
                    'COLBY_SITE_ERRORS_SEND_EMAILS',
                    'Use CBSiteDoesSendEmailErrorReports instead.'
                ],

                // 2017_03_17
                ['COLBY_SITE_NAME', 'Remove it and use site preferences.'],

                // 2017_03_17
                ['CBSiteName', 'Remove it and use site preferences.'],

                // 2017_03_17
                ['CBSiteNameHTML', 'Remove it and use site preferences.'],

                // 2017_03_19
                ['COLBY_SITE_URL', 'Remove it and use site preferences.'],

                // 2017_03_22
                [
                    'CBSiteAdministratorEmail',
                    'Remove it and use site preferences.'
                ],

                // 2017_03_22
                [
                    'COLBY_SITE_ADMINISTRATOR',
                    'Remove it and use site preferences.'
                ],

                // 2017_07_08
                ['COLBY_FACEBOOK_APP_ID', 'Use CBFacebookAppID instead.'],

                // 2017_07_08
                ['COLBY_FACEBOOK_APP_SECRET', 'Use CBFacebookAppSecret instead.'],

                // 2017_07_08
                ['COLBY_MYSQL_HOST', 'Use CBMySQLHost instead.'],

                // 2017_07_08
                ['COLBY_MYSQL_USER', 'Use CBMySQLUser instead.'],

                // 2017_07_08
                ['COLBY_MYSQL_PASSWORD', 'Use CBMySQLPassword instead.'],

                // 2017_07_08
                ['COLBY_MYSQL_DATABASE', 'Use CBMySQLDatabase instead.'],

                // 2018_11_06
                [
                    'STRIPE_PAYMENT_ENABLED',
                    'Enable payments in stripe preferences.',
                ],
            ];

            foreach ($deprecatedConstants as $constant) {
                if (defined($constant[0])) {
                    $issues[] = [
                        $constant[0],
                        'This constant has been deprecated. ' . $constant[1]
                    ];
                }
            }

            if (
                sha1_file(cbsitedir() . '/.gitignore') !==
                sha1_file(cbsysdir() . '/setup/gitignore.template.data')
            ) {
                $message = <<<EOT

                    The website (.gitigore (code)) file is different than the
                    (colby/setup/gitigore.template.data (code)) file.

                EOT;

                array_push($issues, $message);
            }

            if (
                sha1_file(cbsitedir() . '/.htaccess') !==
                sha1_file(cbsysdir() . '/setup/htaccess.template.data')
            ) {
                $message = <<<EOT

                    The website (.htaccess (code)) file is different than the
                    (colby/setup/htaccess.template.data (code)) file.

                EOT;

                array_push($issues, $message);
            }

            /* 2018_05_05 Page kinds should install themselves. */
            if (is_callable('CBPageHelpers::classNamesForPageKinds')) {
                $message = <<<EOT

                    Remove the function
                    (classNamesForPageKinds\(\)(code)) from the class
                    (CBPageHelpers(code)).

                    Page kind classes should install themselves.

                EOT;

                $issues[] = $message;
            }

            /**
             * @NOTE 2017_08_17
             *
             *      Search for use of deprecated "colby/Colby.php" file.
             */

            if (is_file($filepath = cbsitedir() . '/index.php')) {
                $text = file_get_contents($filepath);

                if (preg_match('/Colby\.php/', $text)) {
                    $issues[] = [
                        'colby/Colby.php',
                        'The "index.php" file for this site references the ' .
                        'deprecated "colby/Colby.php" file. Replace this ' .
                        'with a reference to the "colby/init.php" file.'
                    ];
                }
            }

            /* 2018_01_12 Unused function */

            if (is_callable('CBPageHelpers::classNameForPageSettings')) {
                $issues[] = [
                    'CBPageHelpers',
                    'The function CBPageHelpers::classNameForPageSettings() ' .
                    'has been removed with no replacement.'
                ];
            }

            /* 2018_04_11 */

            if (is_callable('CBPageHelpers::classNameForUnsetPageSettings')) {
                $message = <<<EOT

                    Remove the function
                    (classNameForUnsetPageSettings\(\)(code)) from the class
                    (CBPageHelpers(code)).

                    Pages that use page settings are now required to have the
                    (classNamesForSettings(code)) property set on their model.

                    Implement (get\(\)(code)) on
                    (CBPageSettings_defaultClassName(code)) as a temporary
                    workaround.

                EOT;

                $issues[] = $message;
            }

            $issues = array_merge(
                $issues,
                CBAdmin::getIssueMessages()
            );
        }

        return [
            ['CBStatusAdminPage_duplicateURIMessages', $duplicateURIMessages],
            ['CBStatusAdminPage_issues', $issues],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBUI',
            'CBUIMessagePart',
            'CBUISectionItem4',
            'CBUIStringsPart'
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- functions -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function fetchDuplicateURIMessages(): array {
        $messages = [];
        $SQL = <<<EOT

            SELECT      `URI`
            FROM        `ColbyPages`
            WHERE       `published` IS NOT NULL AND
                        `URI` IS NOT NULL
            GROUP BY    `URI`
            HAVING      COUNT(*) > 1

        EOT;

        $URIs = CBDB::SQLToArray($SQL);

        if (!empty($URIs)) {
            foreach ($URIs as $URI) {
                $messages[] = CBStatusAdminPage::duplicateURIToMessage($URI);
            }
        }

        return $messages;
    }
    /* fetchDuplicateURIMessages() */



    /**
     * @return string
     */
    static function duplicateURIToMessage($URI): string {
        $message = '';
        $URIAsSQL = CBDB::stringToSQL($URI);

        $SQL = <<<EOT

            SELECT      LOWER(HEX(CBModels.ID)) AS ID,
                        title

            FROM        CBModels

            JOIN        ColbyPages
                ON      CBModels.ID = ColbyPages.archiveID

            WHERE       URI = {$URIAsSQL} AND
                        published IS NOT NULL

            ORDER BY    published ASC

        EOT;

        $data = CBDB::SQLToObjects($SQL);

        foreach ($data as $datum) {
            $editURL = cbsiteurl() . "/admin/?c=CBModelEditor&ID={$datum->ID}";
            $message .= "\"{$datum->title}\" (edit (a {$editURL}))\n\n";
        }

        $message = <<<EOT

            /{$URI}/

            {$message}

        EOT;

        return $message;
    }
    /* duplicateURIToMessage() */

}
/* CBStatusAdminPage */
