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
        return [CBWellKnownThemeForPageTitleAndDescription::URL('CBWellKnownThemeForPageTitleAndDescription.css')];
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
