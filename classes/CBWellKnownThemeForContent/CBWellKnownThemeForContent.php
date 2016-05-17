<?php

final class CBWellKnownThemeForContent {

    const ID = '0d1bedea8d5e706950f1878ad3aff961ba36b631';

    /**
     * @return null
     */
    public static function install() {
        $spec = CBModels::fetchSpecByID(CBWellKnownThemeForContent::ID);

        if ($spec === false) {
            $spec = (object)[
                'ID' => CBWellKnownThemeForContent::ID,
            ];
        }

        $originalSpec = clone $spec;

        /* set or reset required properties */
        $spec->className = 'CBTheme';
        $spec->classNameForKind = 'CBTextView';
        $spec->classNameForTheme = 'CBWellKnownThemeForContent';
        $spec->description = 'The default theme for CBThemedTextView.';
        $spec->title = 'CBWellKnownThemeForContent';

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
