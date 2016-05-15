<?php

final class CBStandardBodyTextTheme {

    const ID = '0d1bedea8d5e706950f1878ad3aff961ba36b631';

    /**
     * @return null
     */
    public static function install() {
        $spec = CBModels::fetchSpecByID(CBStandardBodyTextTheme::ID);

        if (empty($spec)) {
            $spec = (object)[
                'ID' => CBStandardBodyTextTheme::ID,
            ];
        }

        $originalSpec = clone $spec;

        /* set or reset required properties */
        $spec->className = 'CBTheme';
        $spec->classNameForKind = 'CBTextView';
        $spec->classNameForTheme = 'CBStandardBodyTextTheme';
        $spec->description = 'The default theme for CBThemedTextView.';
        $spec->title = 'CBStandardBodyTextTheme';

        if ($spec != $originalSpec) {
            CBModels::save([$spec]);
        }
    }

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [CBStandardBodyTextTheme::URL('CBStandardBodyTextTheme.css')];
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
