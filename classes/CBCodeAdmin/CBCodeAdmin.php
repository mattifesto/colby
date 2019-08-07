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
            Colby::flexpath(__CLASS__, 'v500.js', cbsysurl()),
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
            'CBMessageMarkup',
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
            'URL' => '/admin/?c=CBCodeAdmin',
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

        $ack = CBModel::valueToString(
            CBSitePreferences::model(),
            'absoluteAckPath'
        );

        if (empty($ack)) {
            $ack = 'ack';
        };

        $command = implode(
            ' ',
            [
                $ack,
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


                /* -- warnings -- -- -- -- -- */

                /**
                 * 2019_06_16
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
                    'severity' => 4,
                    'title' => (
                        'Replace specToDescription() with ' .
                        'CBUISpec_toDescription()'
                    ),
                ],


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
                 * 2019_08_06 (warning)
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => (
                        'adminPagePermissions|' .
                        'adminPageMenuNamePath|' .
                        'adminPageRenderContent'
                    ),
                    'severity' => 4,
                    'title' => (
                        'Replace /admin/page/ interfaces with CBAdmin ' .
                        'interfaces'
                    ),
                ],
            ]
        );
    }
    /* searches() */
}
/* CBCodeAdmin */
