<?php

final class CBPageSettings {

    /* -- functions -- -- -- -- -- */



    /**
     * @param [string] $pageSettingsClassNames
     *
     * @return [string]
     */
    static function htmlElementClassNames(
        array $pageSettingsClassNames
    ): array {
        $htmlElementClassNames = [];

        foreach ($pageSettingsClassNames as $className) {
            $function = "{$className}::CBPageSettings_htmlElementClassNames";

            if (is_callable($function)) {
                $classNames = call_user_func($function);

                $htmlElementClassNames = array_merge(
                    $htmlElementClassNames,
                    $classNames
                );
            }
        }

        return array_values(
            array_unique($htmlElementClassNames)
        );
    }
    /* htmlElementClassNames() */



    /**
     * @param [string] $pageSettingsClassNames
     *
     * @return void
     */
    static function renderHeadElementHTML(
        array $pageSettingsClassNames
    ): void {
        foreach ($pageSettingsClassNames as $className) {
            $function = "{$className}::CBPageSettings_renderHeadElementHTML";

            if (is_callable($function)) {
                call_user_func($function);
            }
        }
    }
    /* renderHeadElementHTML() */



    /**
     * @param string $pageSettingsClassName
     * @param Throwable $throwable
     *
     * @return void
     */
    static function renderErrorPage(
        string $pageSettingsClassName,
        Throwable $throwable
    ): void {
        $function = "{$pageSettingsClassName}::CBPageSettings_renderErrorPage";

        if (is_callable($function)) {
            try {
                call_user_func($function, $throwable);
            } catch (Throwable $innerThrowable) {
                CBErrorHandler::report($innerThrowable);

                CBErrorHandler::renderErrorReportPageForInnerErrorAndExit(
                    $throwable,
                    $innerThrowable
                );
            }
        } else {
            CBErrorHandler::renderErrorReportPage($throwable);
        }
    }
    /* renderErrorPage() */



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
    static function renderPostContentHTML(
        array $pageSettingsClassNames
    ): void {
        foreach ($pageSettingsClassNames as $className) {
            $function = "{$className}::CBPageSettings_renderPostContentHTML";

            if (is_callable($function)) {
                call_user_func($function);
            }
        }
    }
    /* renderPostContentHTML() */



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
    static function renderPreContentHTML(
        array $pageSettingsClassNames
    ): void {
        foreach ($pageSettingsClassNames as $className) {
            $function = "{$className}::CBPageSettings_renderPreContentHTML";

            if (is_callable($function)) {
                call_user_func($function);
            }
        }
    }
    /* renderPreContentHTML() */



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
    static function requiredClassNames(
        array $rootPageSettingsClassNames
    ): array {
        return CBRequiredClassNamesResolver::resolveRequiredClassNames(
            $rootPageSettingsClassNames,
            [
                'CBPageSettings_requiredClassNames',
            ]
        );
    }
    /* requiredClassNames() */

}
