<?php

final class CBUIThemeSelector {

    /**
     * @return array
     */
    static function CBAjax_fetchThemeOptions(stdClass $args): array {
        $classNameForKind = CBModel::valueToString(
            $args,
            'classNameForKind'
        );

        $SQL = <<<EOT

            SELECT  `v`.`modelAsJSON`
            FROM    `CBModels` AS `m`
            JOIN    `CBModelVersions` AS `v` ON
                    `m`.`ID` = `v`.`ID` AND
                    `m`.`version` = `v`.`version`
            WHERE   `m`.`className` = 'CBTheme'

EOT;

        $themes = CBDB::SQLToArray(
            $SQL,
            [
                'valueIsJSON' => true,
            ]
        );

        $themes = array_filter(
            $themes,
            function ($theme) use ($classNameForKind) {
                return $theme->classNameForKind === $classNameForKind;
            }
        );

        $options = array_values(
            array_map(
                function ($theme) {
                    return (object)[
                        'title' => $theme->title,
                        'description' => $theme->description,
                        'value' => $theme->ID,
                    ];
                },
                $themes
            )
        );

        if (is_callable($function = "{$classNameForKind}::themeOptions")) {
            $options = array_merge(
                call_user_func($function),
                $options
            );
        }

        return array_merge(
            [
                (object)[
                    'title' => 'Default',
                ]
            ],
            $options
        );
    }
    /* CBAjax_fetchThemeOptions() */


    /**
     * @return string
     */
    static function CBAjax_fetchThemeOptions_group(): string {
        return 'Administrators';
    }
    /* CBAjax_fetchThemeOptions_group() */


    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v488.js', cbsysurl()),
        ];
    }


    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBUISelector',
            'Colby',
        ];
    }
}
