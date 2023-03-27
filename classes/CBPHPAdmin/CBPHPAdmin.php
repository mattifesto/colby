<?php

final class
CBPHPAdmin
{
    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBAdmin_getIssueCBMessages(
    ): array {
        if (PHP_VERSION_ID < 70400) {
            return [
                CBConvert::stringToCleanLine(<<<EOT

                    Upgrade the PHP version used by this web server to 7.4.

                EOT),
            ];
        } else {
            return [];
        }
    }
    /* CBAdmin_getIssueMessages() */



    /**
     * @return string
     */
    static function CBAdmin_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return [
            'develop',
            'php',
        ];
    }



    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'PHP Administration';
    }



    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        $javaScriptURLs =
        [
            Colby::flexpath(__CLASS__, 'v374.js', cbsysurl()),
        ];

        return $javaScriptURLs;
    }
    // CBHTMLOutput_JavaScriptURLs()



    /**
     * @return [[<name>, <value>]]
     */
    static function
    CBHTMLOutput_JavaScriptVariables(
    ): array
    {
        $values =
        [
            (object)
            [
                'CBPHPAdmin_values_name_property' =>
                'loaded extensions',

                'CBPHPAdmin_values_value_property' =>
                implode(
                    ', ',
                    get_loaded_extensions()
                ),
            ],
        ];

        $iniValues =
        ini_get_all(
            null,
            false
        );

        foreach (
            $iniValues as $iniValueName => $iniValueValue
        ) {
            array_push(
                $values,
                (object)
                [
                    'CBPHPAdmin_values_name_property' =>
                    $iniValueName,

                    'CBPHPAdmin_values_value_property' =>
                    $iniValueValue,
                ]
            );
        }

        $javaScriptVariables =
        [
            [
                'CBPHPAdmin_values',
                $values,
            ],
        ];

        return $javaScriptVariables;
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
        $arrayOfRequiredClassNames =
        [
            'CBJavaScript',
            'CBUI',
            'CBUISectionItem4',
            'CBUIStringsPart',
            'Colby',
        ];

        return $arrayOfRequiredClassNames;
    }
    // CBHTMLOutput_requiredClassNames()



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $updater = CBModelUpdater::fetch(
            (object)[
                'ID' => CBDevelopAdminMenu::getModelCBID(),
            ]
        );

        $items = CBModel::valueToArray(
            $updater->working,
            'items'
        );

        array_push(
            $items,
            (object)[
                'className' => 'CBMenuItem',
                'name' => 'php',
                'text' => 'PHP',
                'URL' => CBAdmin::getAdminPageURL(
                    'CBPHPAdmin'
                ),
            ]
        );

        $updater->working->items = $items;

        CBModelUpdater::save($updater);
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

}
