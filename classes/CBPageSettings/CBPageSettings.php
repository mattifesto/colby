<?php

final class CBPageSettings {

    /**
     * @return ?string
     */
    static function defaultClassName(): ?string {
        if (is_callable($function = 'CBPageSettings_defaultClassName::get')) {
            return call_user_func($function);
        }

        return null;
    }

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
            }
        }

        return array_values(array_unique($htmlElementClassNames));
    }

    /**
     * @param [string] $pageSettingsClassNames
     *
     * @return void
     */
    static function renderHeadElementHTML($pageSettingsClassNames): void {
        foreach ($pageSettingsClassNames as $className) {
            if (is_callable($function = "{$className}::CBPageSettings_renderHeadElementHTML")) {
                call_user_func($function);
            }
        }
    }

    /**
     * @param string $pageSettingsClassName
     * @param Throwable $throwable
     *
     * @return bool
     *
     *      Returns true if a page was rendered; otherwise false.
     */
    static function renderErrorPage($pageSettingsClassName, $throwable): bool {
        if (is_callable($function = "{$pageSettingsClassName}::CBPageSettings_renderErrorPage")) {
            call_user_func($function, $throwable);
            return true;
        } else {
            return false;
        }
    }

    /**
     * This function renders HTML generated by the page settings classes that is
     * intended to be placed before the end tag of the body element. This HTML
     * will be placed after the page content, but may not be the very last HTML
     * of the body element.
     *
     * @param [string] $pageSettingsClassNames
     *
     * @return void
     */
    static function renderPostContentHTML($pageSettingsClassNames): void {
        foreach ($pageSettingsClassNames as $className) {
            if (is_callable($function = "{$className}::CBPageSettings_renderPostContentHTML")) {
                call_user_func($function);
            }
        }
    }

    /**
     * This function renders HTML generated by the page settings classes that is
     * intended to be placed soon after start tag of the body element. This HTML
     * will be placed before the page content, but may not be the very first
     * HTML of the body element.
     *
     * @param [string] $pageSettingsClassNames
     *
     * @return void
     */
    static function renderPreContentHTML($pageSettingsClassNames): void {
        foreach ($pageSettingsClassNames as $className) {
            if (is_callable($function = "{$className}::CBPageSettings_renderPreContentHTML")) {
                call_user_func($function);
            }
        }
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
