<?php

final class CBCodeAdmin {

    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * @param object $args
     *
     * @return [string]
     */
    static function CBAjax_search(stdClass $args): array {
        $index = CBModel::valueAsInt($args, 'index');

        return CBCodeAdmin::search(
            CBCodeAdmin::searches()[$index]
        );
    }
    /* CBAjax_search() */



    /**
     * @return string
     */
    static function CBAjax_search_group(): string {
        return 'Administrators';
    }



    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return [
            'develop',
            'code',
        ];
    }



    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Code Administration';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v500.css', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v545.js', cbsysurl()),
        ];
    }



    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        return [
            [
                'CBCodeAdmin_searches',
                CBCodeAdmin::searches(),
            ],
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBErrorHandler',
            'CBMessageMarkup',
            'CBModel',
            'CBUI',
            'CBUIBooleanSwitchPart',
            'CBUIExpander',
            'Colby',
        ];
    }



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $spec = CBModels::fetchSpecByID(
            CBDevelopAdminMenu::ID()
        );

        $spec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'code',
            'text' => 'Code',
            'URL' => CBAdmin::getAdminPageURL('CBCodeAdmin'),
        ];

        CBDB::transaction(
            function () use ($spec) {
                CBModels::save($spec);
            }
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBDevelopAdminMenu',
        ];
    }



    /* -- functions -- -- -- -- -- */



    /**
     * You can create a class in each library in which to implement the
     * CBCodeAdmin_searches() interface to add searches for that library. You
     * can actually add multiple classes, but the names of all the classes
     * should include "CodeAdmin" because these clases are automatically removed
     * from the search.
     *
     * @NOTE 2019_07_23
     *
     *      Instructions:
     *
     *      Add each new search as a warning as long as the current code will
     *      still work. The developer of a site should fix all warnings before
     *      upgrading because at some point the code will change and the warning
     *      will become an error.
     *
     *      The searches should go in chronological order from when they are
     *      added, not in some form of alphabetical order. This indicates that
     *      found items higher up or more urgent.
     *
     *      An error means that literally the code found will no longer work and
     *      must be fixed before being put into production.
     *
     *      This is currently not perfect as some of the errors below should be
     *      warnings.
     *
     * @param object $search
     *
     * @return [string]
     */
    static function search(stdClass $search): array {
        $output = [];

        $command = implode(
            ' ',
            [
                'ack',
                '--heading',
                // '--underline', (enable only if ack v3 is available)
                "--match '{$search->regex}'",
                '--ignore-dir=data',
                '--ignore-file=match:CodeAdmin',
                CBModel::valueToString($search, 'args'),
            ]
        );

        $filetype = CBModel::valueToString($search, 'filetype');

        switch ($filetype) {
            case 'js':

                $command .= ' --js';
                break;

            case 'php':

                $command .= ' --php';
                break;

            case 'args':

                break;

            default:

                $command .= ' --css --js --php';
                break;
        }

        $pwd = getcwd();

        chdir(cbsitedir());

        try {
            exec("{$command} 2>&1", $output, $exitCode);

            if ($exitCode === 0) {

            } else if ($exitCode !== 1){
                array_push($output, "! returned exit code: {$exitCode}");
            }
        } finally {
            chdir($pwd);
        }

        return $output;
    }
    /* search() */



    /**
     * @return [object]
     */
    static function searches(): array {
        $searches = [];
        $allClassNames = CBAdmin::fetchClassNames();

        foreach ($allClassNames as $className) {
            $function = "{$className}::CBCodeAdmin_searches";

            if (is_callable($function)) {
                $searches = array_merge(
                    $searches,
                    call_user_func($function)
                );
            }
        }

        return array_merge(
            $searches,
            [
                /* errors */

                /**
                 * 2019_06_16
                 */
                (object)[
                    'filetype' => 'js',
                    'regex' => 'CBModelUpdater',
                    'title' => (
                        'The CBModelUpdater JavaScript class has been removed'
                    ),
                ],


                /**
                 * 2019_06_16
                 */
                (object)[
                    'filetype' => 'js',
                    'regex' => 'Colby\\.centsToDollars\\(',
                    'title' => (
                        'Replace use of Colby.centsToDollars() with ' .
                        'CBConvert.centsToDollars()'
                    ),
                ],


                /**
                 * 2019_06_16
                 */
                (object)[
                    'filetype' => 'js',
                    'regex' => 'Colby\\.imageToURL\\(',
                    'title' => (
                        'Replace use of Colby.imageToURL() with ' .
                        'CBImage.toURL()'
                    ),
                ],


                /**
                 * 2019_06_16
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'Colby::reportException\\(',
                    'title' => (
                        'Replace use of Colby::reportException() with ' .
                        'CBErrorHandler::report()'
                    ),
                ],


                /**
                 * 2019_06_16
                 */
                (object)[
                    'filetype' => 'js',
                    'regex' => 'dataStoreFlexpath',
                    'title' => (
                        'Replace use of Colby.dataStoreFlexpath() with ' .
                        'CBDataStore.flexpath()'
                    ),
                ],


                /**
                 * 2019_06_16
                 */
                (object)[
                    'filetype' => 'js',
                    'regex' => 'dataStoreIDToURI',
                    'title' => (
                        'Replace use of Colby.dataStoreIDToURI() with ' .
                        'CBDataStore.flexpath()'
                    ),
                ],


                /**
                 * 2019_06_16
                 */
                (object)[
                    'filetype' => 'js',
                    'regex' => 'setPanelContent\\(',
                    'title' => 'Colby.setPanelContent() has been removed',
                ],


                /**
                 * 2019_06_16
                 */
                (object)[
                    'filetype' => 'js',
                    'regex' => 'warnOlderBrowsers\\(',
                    'title' => 'Colby.warnOlderBrowsers() has been removed',
                ],


                /**
                 * 2019_06_22
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'ColbyRequest::isForFrontPage\\(',
                    'title' => (
                        'ColbyRequest::isForFrontPage() replaced with ' .
                        'ColbyRequest::currentRequestIsForTheFrontPage()'
                    ),
                ],


                /**
                 * 2019_06_26
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'CBSitePreferences::siteURL\\(',

                    'title' =>
                    'Replace CBSitePreferences::siteURL() with cbsiteurl()',
                ],


                /**
                 * 2019_07_07
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'CBView::renderModelAsHTML',
                    'title' => 'Replace use of CBView::renderModelAsHTML()',
                ],


                /**
                 * 2019_07_08
                 */
                (object)[
                    'args' => '--php --js --css',
                    'filetype' => 'args',
                    'regex' => 'CBUIImageUploader',
                    'title' => 'Replace use of CBUIImageUploader',
                ],


                /**
                 * 2019_07_09
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'CBView::modelWithClassName',
                    'title' => 'Replace use of CBView::modelWithClassName()',
                ],


                /**
                 * 2019_07_09
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => ' renderModelAsHTML',
                    'title' => (
                        'Replace renderModelAsHTML() with CBView_render()'
                    ),
                ],


                /**
                 * 2019_07_09
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'ColbyConvert::centsIntToDollarsString',
                    'title' => (
                        'Replace use of ' .
                        'ColbyConvert::centsIntToDollarsString() with ' .
                        'CBConvert::centsToDollars()'
                    ),
                ],


                /**
                 * 2019_07_09
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'ColbyConvert::markaroundToHTML',
                    'title' => (
                        'Replace use of ColbyConvert::markaroundToHTML() ' .
                        'with CBMarkaround::markaroundToHTML()'
                    ),
                ],


                /**
                 * 2019_07_09
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'ColbyConvert::textToFormattedContent',
                    'title' => (
                        'Replace use of ' .
                        'ColbyConvert::textToFormattedContent() with ' .
                        'CBMarkaround::markaroundToHTML()'
                    ),
                ],


                /**
                 * 2019_07_09
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'ColbyConvert::textToHTML',
                    'title' => (
                        'Replace use of ColbyConvert::textToHTML() ' .
                        'with cbhtml()'
                    ),
                ],


                /**
                 * 2019_07_09
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'ColbyConvert::textToLines',
                    'title' => (
                        'Replace use of ColbyConvert::textToLines() with ' .
                        'CBConvert::stringToLines()'
                    ),
                ],


                /**
                 * 2019_07_09
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'ColbyConvert::textToSQL',
                    'title' => (
                        'Replace use of ColbyConvert::textToSQL() with ' .
                        'CBDB::escapeString()'
                    ),
                ],


                /**
                 * 2019_07_09
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'ColbyConvert::textToStub',
                    'title' => (
                        'Replace use of ColbyConvert::textToStub() with ' .
                        'CBConvert::stringToStub()'
                    ),
                ],


                /**
                 * 2019_07_28 (warning)
                 * 2019_07_30 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => (
                        'handle-authorization-failed-ajax.php'
                    ),
                    'title' => (
                        'Replace use of handle-authorization-failed-ajax.php ' .
                        'with CBAjax interfaces.'
                    ),
                ],


                /**
                 * 2019_07_26 (warning)
                 * 2019_08_03 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => (
                        'CBPagesPreferences::defaultClassNamesForSupportedViews'
                    ),
                    'severity' => 3,
                    'title' => (
                        'Implement CBInstall_install() on any views in the ' .
                        'constant CBPagesPreferences::' .
                        'defaultClassNamesForSupportedViews, then remove ' .
                        'them from the constant, then eventually remove ' .
                        'the constant.'
                    ),
                ],


                /**
                 * 2019_06_22 (warning)
                 * 2019_08_04 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'CBAjaxResponse',
                    'title' => (
                        'Replace use of CBAjaxResponse with a CBAjax interface'
                    ),
                ],


                /**
                 * 2019_07_05 (warning)
                 * 2019_08_04 (error)
                 */
                (object)[
                    'filetype' => 'js',
                    'regex' => 'class=CBImages&function=upload',
                    'title' => (
                        'Replace deprecated CBImages ajax upload function'
                    ),
                ],


                /**
                 * 2019_08_06 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => (
                        'ColbyUser::addUserToGroup\\('
                    ),
                    'title' => (
                        'Replace ColbyUser::addUserToGroup() with ' .
                        'Colby::updateGroupMembership()'
                    ),
                ],


                /**
                 * 2019_08_06 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => (
                        'COLBY_SYSTEM_URL'
                    ),
                    'title' => (
                        'Replace COLBY_SYSTEM_URL with cbsysurl()'
                    ),
                ],


                /**
                 * 2019_07_23 (warning)
                 * 2019_08_06 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'CBSystemURL',
                    'title' => 'Replace CBSystemURL with cbsysurl()',
                ],


                /**
                 * 2019_07_16 (warning)
                 * 2019_08_07 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'ColbyUser::currentUserHash',
                    'title' => (
                        'Replace ColbyUser::currentUserHash() with ' .
                        'ColbyUser::getCurrentUserID()'
                    ),
                ],


                /**
                 * 2019_07_23 (warning)
                 * 2019_08_07 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'function\s+requiredClassNames\\(\s*\\)',
                    'title' => (
                        'Replace requiredClassNames() with ' .
                        'CBHTMLOutput_requiredClassNames()'
                    ),
                ],


                /**
                 * 2019_07_23 (warning)
                 * 2019_08_07 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'function\s+requiredJavaScriptURLs\\(',
                    'title' => (
                        'Replace requiredJavaScriptURLs() with ' .
                        'CBHTMLOutput_JavaScriptURLs()'
                    ),
                ],


                /**
                 * 2019_07_23 (warning)
                 * 2019_08_07 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'Colby::flexnameForJavaScriptForClass\\(',
                    'title' => (
                        'Replace Colby::flexnameForJavaScriptForClass() ' .
                        'with Colby::flexpath()'
                    ),
                ],


                /**
                 * 2019_07_24 (warning)
                 * 2019_08_07 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'Colby::flexnameForCSSForClass\\(',
                    'title' => (
                        'Replace Colby::flexnameForCSSForClass() with ' .
                        'Colby::flexpath()'
                    ),
                ],


                /**
                 * 2019_08_04 (warning)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'CBHTMLOutput::\$classNameForSettings',
                    'title' => (
                        'Replace use of CBHTMLOutput::$classNameForSettings ' .
                        'with CBHTMLOutput::pageInformation()'
                    ),
                ],


                /**
                 * 2019_08_06 (warning)
                 * 2019_08_10 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => (
                        'adminPagePermissions|' .
                        'adminPageMenuNamePath|' .
                        'adminPageRenderContent'
                    ),
                    'title' => (
                        'Replace /admin/page/ interfaces with CBAdmin ' .
                        'interfaces'
                    ),
                ],


                /**
                 * 2019_09_19 (warning)
                 * 2019_09_26 (error)
                 */
                (object)[
                    'filetype' => 'js',
                    'regex' => 'Colby\.alert\(',
                    'severity' => 3,
                    'title' => (
                        'Replace use of Colby.alert() with ' .
                        'CBUIPanel.displayText().'
                    ),
                ],


                /**
                 * 2019_09_19 (warning)
                 * 2019_09_26 (error)
                 */
                (object)[
                    'args' => '--ignore-file=match:CBErrorHandler.js',
                    'filetype' => 'js',
                    'regex' => 'Colby\.(displayError|displayAndReportError)',
                    'severity' => 3,
                    'title' => (
                        'Replace use of Colby.displayError() and ' .
                        'Colby.displayAndReportError() with ' .
                        'CBUIPanel.displayError().'
                    ),
                ],


                /**
                 * 2019_09_19 (warning)
                 * 2019_09_26 (error)
                 */
                (object)[
                    'filetype' => 'js',
                    'regex' => 'Colby\.(displayResponse|displayXHRError)',
                    'severity' => 3,
                    'title' => (
                        'Replace use of Colby.displayResponse() and ' .
                        'Colby.displayXHRError() with ' .
                        'CBUIPanel.displayAjaxResponse().'
                    ),
                ],


                /**
                 * 2019_09_19 (warning)
                 * 2019_09_26 (error)
                 */
                (object)[
                    'filetype' => 'js',
                    'regex' => 'Colby\.(createPanel|setPanelElement|setPanelText|showPanel)',
                    'severity' => 3,
                    'title' => (
                        'Replace use of Colby.createPanel() and ' .
                        'Colby.setPanelElement() and ' .
                        'Colby.setPanelText() and ' .
                        'Colby.showPanel() with ' .
                        'CBUIPanel.'
                    ),
                ],


                /**
                 * 2019_06_16 (warning)
                 * 2019_10_11 (error)
                 */
                (object)[
                    'args' => implode(
                        ' ',
                        [
                            '--ignore-file=match:CBUISpec.js',
                            '--ignore-file=match:CBUISpec_Tests.js',
                        ]
                    ),
                    'filetype' => 'js',
                    'regex' => '(?<!CBUISpec\\.)specToDescription',
                    'severity' => 3,
                    'title' => (
                        'Replace specToDescription() with ' .
                        'CBUISpec_toDescription()'
                    ),
                ],


                /**
                 * 2019_08_20 (warning)
                 * 2019_10_11 (error)
                 */
                (object)[
                    'filetype' => 'js',
                    'regex' => 'specToThumbnailURI|CBUISpec_toThumbnailURI',
                    'severity' => 3,
                    'title' => (
                        'Replace "specToThumbnailURI" or ' .
                        '"CBUISpec_toThumbnailURI" interface ' .
                        'implementations with a "CBUISpec_toThumbnailURL" ' .
                        'interface implementation.'
                    ),
                ],


                /**
                 * 2019_08_15 (warning)
                 * 2019_10_30 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'CBTests_classTest',
                    'severity' => 3,
                    'title' => (
                        'Replace CBTests_classTest() with an actual test.'
                    ),
                ],


                /**
                 * 2019_08_15 (warning)
                 * 2019_11_04 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'resultAndExpectedToMessage',
                    'severity' => 3,
                    'title' => (
                        'Replace ' .
                        'CBConvertTests::resultAndExpectedToMessage() ' .
                        'with CBTest::resultMismatchFailure().'
                    ),
                ],


                /**
                 * 2019_10_31 (warning)
                 * 2019_11_04 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'CBUnitTests_tests',
                    'severity' => 3,
                    'title' => (
                        'Replace CBUnitTests_tests() with CBTest_getTests().'
                    ),
                ],


                /**
                 * 2019_11_09 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'isLoggedIn',
                    'severity' => 3,
                    'title' => (
                        'Replace use of ->isLoggedIn() with ' .
                        'ColbyUser::currentUserIsLoggedIn()'
                    ),
                ],


                /**
                 * 2019_11_09 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'isOneOfThe',
                    'severity' => 3,
                    'title' => (
                        'Replace use of ->isOneOfThe() with ' .
                        'ColbyUser::isMemberOfGroup() or ' .
                        'ColbyUser::currentUserIsMemberOfGroup()'
                    ),
                ],


                /**
                 * 2019_11_09 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'fetchUserDataByHash',
                    'severity' => 3,
                    'title' => (
                        'Replace use of ColbyUser::fetchUserDataByHash() ' .
                        'with CBModels::fetchModelByIDNullable().'
                    ),
                ],


                /**
                 * 2019_11_09 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'userRow',
                    'severity' => 3,
                    'title' => (
                        'Replace use of userRow() with CBUser models.'
                    ),
                ],


                /**
                 * 2019_11_09 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'timestampToFriendlyTime',
                    'severity' => 3,
                    'title' => (
                        'Replace use of ' .
                        'ColbyConvert::timestampToFriendlyTime() with ' .
                        'client side timestamp conversons.'
                    ),
                ],


                /**
                 * 2019_11_09 (error)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'timestampToLocalUserTime',
                    'severity' => 3,
                    'title' => (
                        'Replace use of ' .
                        'ColbyConvert::timestampToLocalUserTime() with ' .
                        'client side timestamp conversons.'
                    ),
                ],


                /**
                 * 2019_11_10 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        The ColbyUser class can no longer construct instances.

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'ColbyUser::current\(',
                    'severity' => 3,
                    'title' => 'ColbyUser::current()',
                ],


                /**
                 * 2019_11_10 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        The ColbyUser class can no longer construct instances.

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'new\s+ColbyUser\(',
                    'severity' => 3,
                    'title' => 'new ColbyUser()',
                ],


                /**
                 * 2019_11_10 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Use ColbyUser::facebookUserIDToCBUserIDs().

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'facebookUserIDtoUserIdentity',
                    'severity' => 3,
                    'title' => 'ColbyUser::facebookUserIDtoUserIdentity()',
                ],


                /**
                 * 2019_11_17 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Use CBUserGroup::fetchCBUserGroupModels().

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'fetchGroupNames\(',
                    'severity' => 3,
                    'title' => 'ColbyUser::fetchGroupNames()',
                ],


                /**
                 * 2019_11_17 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Use CBUserGroup models.

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'CBUsers::installUserGroup\(',
                    'severity' => 3,
                    'title' => 'CBUsers::installUserGroup()',
                ],


                /**
                 * 2019_11_17 (error)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Use CBUserGroup models.

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'CBUsers::uninstallUserGroup\(',
                    'severity' => 3,
                    'title' => 'CBUsers::uninstallUserGroup()',
                ],



                /* -- warnings -- -- -- -- -- */



                /**
                 * 2019_06_26
                 */
                (object)[
                    'args' => '--ignore-file=match:init.php',
                    'filetype' => 'php',
                    'regex' => 'COLBY_SITE_DIRECTORY',
                    'severity' => 4,
                    'title' => 'Replace COLBY_SITE_DIRECTORY with cbsitedir()',
                ],


                /**
                 * 2019_06_26
                 */
                (object)[
                    'args' => '--ignore-file=match:init.php',
                    'filetype' => 'php',
                    'regex' => 'CBSiteDirectory',
                    'severity' => 4,
                    'title' => 'Replace CBSiteDirectory with cbsitedir()',
                ],


                /**
                 * 2019_06_26
                 */
                (object)[
                    'args' => '--ignore-file=match:init.php',
                    'filetype' => 'php',
                    'regex' => 'COLBY_SYSTEM_DIRECTORY',
                    'severity' => 4,
                    'title' => 'Replace COLBY_SYSTEM_DIRECTORY with cbsysdir()',
                ],


                /**
                 * 2019_06_26
                 */
                (object)[
                    'args' => '--ignore-file=match:init.php',
                    'filetype' => 'php',
                    'regex' => 'CBSystemDirectory',
                    'severity' => 4,
                    'title' => 'Replace CBSystemDirectory with cbsysdir()',
                ],


                /**
                 * 2019_07_05
                 */
                (object)[
                    'args' => '--ignore-file=match:CBThemedMenuView',
                    'filetype' => 'php',
                    'regex' => 'CBThemedMenuView',
                    'severity' => 4,
                    'title' => 'Replace use of CBThemedMenuView',
                ],


                /**
                 * 2019_07_09
                 */
                (object)[
                    'args' => '--ignore-file=match:CBModel\.php',
                    'filetype' => 'php',
                    'regex' => 'CBModel_toModel',
                    'severity' => 4,
                    'title' => 'Rename CBModel_toModel() to CBModel_build()',
                ],


                /**
                 * 2019_07_09
                 */
                (object)[
                    'args' => '--ignore-file=match:CBModel\.php',
                    'filetype' => 'php',
                    'regex' => 'modelToSearchText',
                    'severity' => 4,
                    'title' => (
                        'Rename modelToSearchText() to CBModel_toSearchText()'
                    ),
                ],


                /**
                 * 2019_07_09
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'CBModels::modelWithClassName',
                    'severity' => 4,
                    'title' => (
                        'Replace use of CBModels::modelWithClassName()'
                    ),
                ],


                /**
                 * 2019_07_16
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'ColbyUser::currentUserId',
                    'severity' => 4,
                    'title' => (
                        'Use hexadecimal user IDs instead of numeric ' .
                        'user IDs and use the function ' .
                        'ColbyUser::getCurrentUserID()'
                    ),
                ],


                /**
                 * 2019_07_23
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'function\s+requiredCSSURLs\\(',
                    'severity' => 4,
                    'title' => (
                        'Replace requiredCSSURLs() with ' .
                        'CBHTMLOutput_CSSURLs()'
                    ),
                ],


                /**
                 * 2019_07_23
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'function\s+requiredJavaScriptVariables\\(',
                    'severity' => 4,
                    'title' => (
                        'Replace requiredJavaScriptVariables() with ' .
                        'CBHTMLOutput_JavaScriptVariables()'
                    ),
                ],


                /**
                 * 2019_07_23
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'Colby::URLForJavaScriptForSiteClass\\(',
                    'severity' => 4,
                    'title' => (
                        'Replace Colby::URLForJavaScriptForSiteClass() with ' .
                        'Colby::flexpath()'
                    ),
                ],


                /**
                 * 2019_07_26
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'CBPagesPreferences::ID',
                    'severity' => 4,
                    'title' => (
                        'Replace CBPagesPreferences::ID with ' .
                        'CBPagesPreferences::modelID()'
                    ),
                ],


                /**
                 * 2019_07_27
                 */
                (object)[
                    'filetype' => 'js',
                    'regex' => (
                        'CBUIImageChooser.createFullSizedChooser\\('
                    ),
                    'severity' => 4,
                    'title' => (
                        'Replace CBUIImageChooser.createFullSizedChooser() ' .
                        'with CBUIImageChooser.create().'
                    ),
                ],


                /**
                 * 2019_07_27
                 */
                (object)[
                    'filetype' => 'js',
                    'regex' => (
                        'CBUIImageChooser.createThumbnailSizedChooser\\('
                    ),
                    'severity' => 4,
                    'title' => (
                        'Replace ' .
                        'CBUIImageChooser.createThumbnailSizedChooser() ' .
                        'with CBUIImageChooser.create().'
                    ),
                ],


                /**
                 * 2019_08_10
                 */
                (object)[
                    'args' => (
                        '--php --js ' .
                        '--ignore-file=match:CBUpgradesForVersion545.php'
                    ),
                    'cbmessage' => <<<EOT

                        The ColbyPages table "publishedBy" column and the
                        CBViewPage model "publishedBy" property are deprecated
                        and being replaced because they use numeric user IDs.

                    EOT,
                    'regex' => 'publishedBy',
                    'severity' => 4,
                    'title' => 'publishedBy',
                ],


                /**
                 * 2019_08_10
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'currentUserNumericID',
                    'severity' => 4,
                    'title' => (
                        'Replace use of currentUserNumericID with ' .
                        'the hex user ID'
                    ),
                ],


                /**
                 * 2019_08_18
                 */
                (object)[
                    'filetype' => 'js',
                    'regex' => 'navigateToItemCallback',
                    'severity' => 4,
                    'title' => (
                        'Remove use of "navigateToItemCallback". Any ' .
                        'navigation should be done using ' .
                        'CBUINavigationView.navigate() directly.'
                    ),
                ],


                /**
                 * 2019_08_20
                 */
                (object)[
                    'args' => '--ignore-file=match:CBUIImageChooser.js',
                    'filetype' => 'js',
                    'regex' => 'setImageURLCallback',
                    'severity' => 4,
                    'title' => (
                        'Replace use of "setImageURLCallback" with the ' .
                        '"src" property on the CBUIImageChooser object.'
                    ),
                ],


                /**
                 * 2019_08_20
                 */
                (object)[
                    'args' => '--ignore-file=match:CBUIImageChooser.js',
                    'filetype' => 'js',
                    'regex' => 'setImageURI',
                    'severity' => 4,
                    'title' => (
                        'Replace use of "setImageURI" with the ' .
                        '"src" property on the CBUIImageChooser object.'
                    ),
                ],


                /**
                 * 2019_09_19 (warning)
                 */
                (object)[
                    'filetype' => 'js',
                    'regex' => 'CBUIPanel\.(buttons|isShowing|message|reset)',
                    'severity' => 4,
                    'title' => (
                        'Replace use of deprecated CBUIPanel API with ' .
                        'CBUIPanel.displayElement().'
                    ),
                ],


                /**
                 * 2019_11_09 (warning)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'ColbyUsers',
                    'severity' => 4,
                    'title' => (
                        'Replace use of the ColbyUsers table with CBUser ' .
                        'models.'
                    ),
                ],


                /**
                 * 2019_11_10 (warning)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Use CBDB::SQLToObjectNullable().

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'CBDB::SQLToObject\(',
                    'severity' => 4,
                    'title' => 'CBDB::SQLToObject()',
                ],


                /**
                 * 2019_11_18 (warning)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Use CBUserGroup models.

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'CBHex160',
                    'severity' => 4,
                    'title' => 'CBHex160',
                ],


                /**
                 * 2019_11_18 (warning)
                 */
                (object)[
                    'cbmessage' => <<<EOT

                        Page models should have a page settings class name set
                        on a property.

                    EOT,
                    'filetype' => 'php',
                    'regex' => 'CBPageSettings::defaultClassName',
                    'severity' => 4,
                    'title' => 'CBPageSettings::defaultClassName()',
                ],
            ]
        );
    }
    /* searches() */
}
/* CBCodeAdmin */
