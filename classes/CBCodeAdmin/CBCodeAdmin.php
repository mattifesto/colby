<?php

final class CBCodeAdmin {

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
            Colby::flexpath(__CLASS__, 'v474.js', cbsysurl()),
        ];
    }


    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        return [
            [
                'CBCodeAdmin_results',
                CBCodeAdmin::results(),
            ],
        ];
    }


    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBUI',
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
     * @return [string]
     */
    static function results(): array {
        $issues = [

            /**
             * Use CBConvert
             */
            (object)[
                'regex' => 'Colby\\.centsToDollars\\(',
                'filetype' => 'js',
            ],

            /**
             * Use CBConvert
             */
            (object)[
                'regex' => 'Colby\\.imageToURL\\(',
                'filetype' => 'js',
            ],

            /**
             * Removed
             */
            (object)[
                'regex' => 'CBModelUpdater',
                'filetype' => 'js',
            ],

            /**
             * Use CBDataStore.flexpath()
             */
            (object)[
                'regex' => 'dataStoreFlexpath',
                'filetype' => 'js',
            ],

            /**
             * Use CBDataStore.flexpath()
             */
            (object)[
                'regex' => 'dataStoreIDToURI',
                'filetype' => 'js',
            ],

            /**
             * Colby.setPanelContent() has been removed.
             */
            (object)[
                'regex' => 'setPanelContent\\(',
                'filetype' => 'js',
            ],

            /**
             * Colby.warnOlderBrowsers() has been removed.
             */
            (object)[
                'regex' => 'warnOlderBrowsers\\(',
                'filetype' => 'js',
            ],

            /* lower priority searches */

            /**
             * Update interface function name
             */
            (object)[
                'regex' => '(?<!CBUISpec\\.)specToDescription',
                'filetype' => 'js',
                'args' => implode(
                    ' ',
                    [
                        '--ignore-file=match:CBUISpec.js',
                        '--ignore-file=match:CBUISpec_Tests.js',
                    ]
                ),
            ],
        ];

        $output = [];
        $exitCode;

        foreach ($issues as $issue) {
            $command = implode(
                ' ',
                [
                    '/usr/local/bin/ack',
                    '--heading',
                    '--underline',
                    "--match '{$issue->regex}'",
                    CBModel::valueToString($issue, 'args'),
                ]
            );

            switch ($issue->filetype) {
                case 'js':

                    $command .= ' --js';
                    break;

                default:

                    throw new Exception('unknown filetype');
            }

            CBGit::exec(
                $command,
                $output,
                $exitCode
            );

            array_push($output, '');
            array_push($output, '');
        }

        return $output;
    }
    /* results() */
}
/* CBCodeAdmin */
