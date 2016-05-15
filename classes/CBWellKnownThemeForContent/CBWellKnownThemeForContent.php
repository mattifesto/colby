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
        return [CBWellKnownThemeForContent::URL('CBWellKnownThemeForContent.css')];
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
