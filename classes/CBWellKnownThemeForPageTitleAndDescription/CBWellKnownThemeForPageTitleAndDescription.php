<?php

final class CBWellKnownThemeForPageTitleAndDescription {

    const ID = '664d22662308f7443e4c3b43683d4934de087b86';

    /**
     * @return null
     */
    public static function install() {
        $spec = CBModels::fetchSpecByID(CBWellKnownThemeForPageTitleAndDescription::ID);

        if ($spec === false) {
            $spec = (object)[
                'ID' => CBWellKnownThemeForPageTitleAndDescription::ID,
            ];
        }

        $originalSpec = clone $spec;

        /* set or reset required properties */
        $spec->className = 'CBTheme';
        $spec->classNameForKind = 'CBPageTitleAndDescriptionView';
        $spec->classNameForTheme = 'CBWellKnownThemeForPageTitleAndDescription';
        $spec->description = 'The default theme for CBPageTitleAndDescriptionView.';
        $spec->title = 'CBWellKnownThemeForPageTitleAndDescription';

        if ($spec != $originalSpec) {
            CBModels::save([$spec]);
        }
    }

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        $className = __CLASS__;
        $URLs = [Colby::URLForCSSForClass(CBSystemURL, $className)];

        if (is_file(CBSiteDirectory . "/classes/{$className}/{$className}.css")) {
            $URLs[] = Colby::URLForCSSForClass(CBSiteURL, $className);
        }

        return $URLs;
    }
}
