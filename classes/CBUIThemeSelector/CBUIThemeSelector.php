<?php

final class CBUIThemeSelector {

    /**
     * @return null
     */
    static function fetchThemeOptionsForAjax() {
        $response = new CBAjaxResponse();
        $classNameForKind = $_POST['classNameForKind'];

        $SQL = <<<EOT

            SELECT  `v`.`modelAsJSON`
            FROM    `CBModels` AS `m`
            JOIN    `CBModelVersions` AS `v` ON `m`.`ID` = `v`.`ID` AND `m`.`version` = `v`.`version`
            WHERE   `m`.`className` = 'CBTheme'

EOT;

        $themes = CBDB::SQLToArray($SQL, ['valueIsJSON' => true]);
        $themes = array_filter($themes, function ($theme) use ($classNameForKind) {
            return $theme->classNameForKind === $classNameForKind;
        });
        $options = array_values(array_map(function ($theme) {
            return (object)[
                'title' => $theme->title,
                'description' => $theme->description,
                'value' => $theme->ID,
            ];
        }, $themes));

        if (is_callable($function = "{$classNameForKind}::themeOptions")) {
            $options = array_merge(call_user_func($function), $options);
        }

        $response->options = $options;
        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return stdClass
     */
    static function fetchThemeOptionsForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBUI', 'CBUISelector'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [CBUIThemeSelector::URL('CBUIThemeSelector.js')];
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
