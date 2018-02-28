<?php

final class CBPageSettings {

    /**
     * @param [string] $pageSettingsClassNames
     *
     * @return [string]
     */
    static function htmlElementClassNames(array $pageSettingsClassNames): array {
        $htmlElementClassNames = [];

        foreach ($pageSettingsClassNames as $className) {
            if (is_callable($function = "{$className}::CBPageSettings_htmlElementClassNames")) {
                $classNames = call_user_func($function);
                $htmlElementClassNames = array_merge($htmlElementClassNames, $classNames);
            } error_log(json_encode($htmlElementClassNames));
        }

        return array_values(array_unique($htmlElementClassNames));
    }

    /**
     * @param [string] $rootPageSettingsClassNames
     *
     *      An array of root level page settings class names. Often this array
     *      will only contain one root page settings class name.
     *
     * @return [string]
     *
     *      The full array of page settings class names in dependency order.
     */
    static function requiredClassNames(array $rootPageSettingsClassNames): array {
        return CBRequiredClassNamesResolver::resolveRequiredClassNames(
            $rootPageSettingsClassNames,
            ['CBPageSettings_requiredClassNames']
        );
    }
}
