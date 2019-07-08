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
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v481.js', cbsysurl()),
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
     * @param object $search
     *
     * @return [string]
     */
    static function search(stdClass $search): array {
        $output = [];

        $command = implode(
            ' ',
            [
                '/usr/local/bin/ack',
                '--heading',
                '--underline',
                "--match '{$search->regex}'",
                '--ignore-dir=data',
                '--ignore-file=match:CodeAdmin',
                CBModel::valueToString($search, 'args'),
            ]
        );

        switch ($search->filetype) {
            case 'js':

                $command .= ' --js';
                break;

            case 'php':

                $command .= ' --php';
                break;

            case 'args':

                break;

            default:

                throw new Exception('unknown filetype');
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

                    'title' =>
                    'The CBModelUpdater JavaScript class has been removed',
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
                 * 2019_07_05
                 */
                (object)[
                    'args' => '--ignore-file=match:CBThemedMenuView',
                    'filetype' => 'php',
                    'regex' => 'CBThemedMenuView',
                    'title' => 'Replace use of CBThemedMenuView',
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
                 * 2019_07_07
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'CBView::renderModelAsHTML',
                    'title' => 'Replace use of CBView::renderModelAsHTML()',
                ],


                /**
                 * 2019_06_16
                 * Use CBConvert
                 */
                (object)[
                    'filetype' => 'js',
                    'regex' => 'Colby\\.centsToDollars\\(',
                    'title' => 'Colby.centsToDollars()',
                ],


                /**
                 * 2019_06_16
                 * Use CBConvert
                 */
                (object)[
                    'filetype' => 'js',
                    'regex' => 'Colby\\.imageToURL\\(',
                    'title' => 'Colby.imageToURL()',
                ],


                /**
                 * 2019_06_16
                 * Use CBErrorHandler::report()
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'Colby::reportException\\(',

                    'title' =>
                    'Colby::reportException() replaced with ' .
                    'CBErrorHandler::report()',
                ],


                /**
                 * 2019_06_16
                 * Use CBDataStore.flexpath()
                 */
                (object)[
                    'filetype' => 'js',
                    'regex' => 'dataStoreFlexpath',
                    'title' => 'Colby.dataStoreFlexpath()',
                ],


                /**
                 * 2019_06_16
                 * Use CBDataStore.flexpath()
                 */
                (object)[
                    'filetype' => 'js',
                    'regex' => 'dataStoreIDToURI',
                    'title' => 'Colby.dataStoreIDToURI()',
                ],


                /**
                 * 2019_06_16
                 * Colby.setPanelContent() has been removed.
                 */
                (object)[
                    'filetype' => 'js',
                    'regex' => 'setPanelContent\\(',
                    'title' => 'Colby.setPanelContent()',
                ],


                /**
                 * 2019_06_16
                 * Colby.warnOlderBrowsers() has been removed.
                 */
                (object)[
                    'filetype' => 'js',
                    'regex' => 'warnOlderBrowsers\\(',
                    'title' => 'Colby.warnOlderBrowsers()',
                ],


                /**
                 * 2019_06_22
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'ColbyRequest::isForFrontPage\\(',

                    'title' =>
                    'ColbyRequest::isForFrontPage() replaced with ' .
                    'ColbyRequest::currentRequestIsForTheFrontPage()',
                ],


                /* -- warnings -- -- -- -- -- */

                /**
                 * 2019_07_05
                 */
                (object)[
                    'filetype' => 'js',
                    'regex' => 'class=CBImages&function=upload',
                    'severity' => 4,
                    'title' =>
                    'Replace deprecated CBImages ajax upload function',
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
                 * 2019_06_22
                 */
                (object)[
                    'filetype' => 'php',
                    'regex' => 'new\\s+CBAjaxResponse\\(',
                    'severity' => 4,
                    'title' => 'Replace CBAjaxResponse with CBAjax interfaces',
                ],


                /**
                 * 2019_06_16
                 * Update interface function name
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

                    'title' =>
                    'specToDescription() interface functions should have the ' .
                    'name CBUISpec_toDescription()',
                ],
            ]
        );
    }
    /* searches() */
}
/* CBCodeAdmin */
