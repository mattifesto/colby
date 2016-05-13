<?php

final class CBStandardPageTitleAndDescriptionTheme {

    const ID = '664d22662308f7443e4c3b43683d4934de087b86';

    /**
     * @return null
     */
    public static function install() {
        $spec = CBModels::fetchSpecByID(CBStandardPageTitleAndDescriptionTheme::ID);

        if (empty($spec)) {
            $spec = (object)[
                'ID' => CBStandardPageTitleAndDescriptionTheme::ID,
            ];
        }

        $originalSpec = clone $spec;

        /* set or reset required properties */
        $spec->className = 'CBTheme';
        $spec->classNameForKind = 'CBPageTitleAndDescriptionView';
        $spec->classNameForTheme = 'CBStandardPageTitleAndDescriptionTheme';
        $spec->description = 'The default theme for CBPageTitleAndDescriptionView.';
        $spec->title = 'CBStandardPageTitleAndDescriptionTheme';

        if ($spec != $originalSpec) {
            CBModels::save([$spec]);
        }
    }

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [CBStandardPageTitleAndDescriptionTheme::URL('CBStandardPageTitleAndDescriptionTheme.css')];
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
