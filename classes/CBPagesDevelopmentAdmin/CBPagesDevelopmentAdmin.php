<?php

final class CBPagesDevelopmentAdmin {

    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return ['pages', 'develop'];
    }

    /**
     * @return string
     */
    static function CBAdmin_group() {
        return 'Developers';
    }

    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::setTitleHTML('Pages Development Admimistration');
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v380.js', cbsysurl())];
    }

    /**
     * Get a list of all of the pages. This code is written with the
     * understanding that all pages should have a model. We will warn the
     * administrator of pages that don't have a model.
     *
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        $SQL = <<< EOT

            SELECT      LOWER(HEX(ColbyPages.archiveID)) as ID,
                        CBModels.className as className,
                        ColbyPages.classNameForKind as classNameForKind,
                        ColbyPages.published as published,
                        CBModels.title as title
            FROM        ColbyPages
            LEFT JOIN   CBModels ON
                        ColbyPages.archiveID = CBModels.ID
            ORDER BY    ISNULL(published), className, classNameForKind, title

EOT;

        return [
            ['CBPagesDevelopmentAdmin_pages', CBDB::SQLToObjects($SQL)],
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBUI', 'CBUIExpander', 'CBUINavigationArrowPart',
                'CBUISectionItem4', 'CBUIStringsPart'];
    }
}
