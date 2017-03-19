<?php

final class CBWellKnownThemeForContent {

    const ID = '0d1bedea8d5e706950f1878ad3aff961ba36b631';

    /**
     * @return null
     */
    static function install() {
        $originalSpec = CBModels::fetchSpecByID(CBWellKnownThemeForContent::ID);

        if (empty($originalSpec)) {
            $spec = (object)['ID' => CBWellKnownThemeForContent::ID];
        } else {
            $spec = clone $originalSpec;
        }

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
    static function requiredCSSURLs() {
        $className = __CLASS__;
        $URLs = [Colby::flexnameForCSSForClass(CBSystemURL, $className)];

        if (is_file(Colby::flexnameForCSSForClass(CBSiteDirectory, $className))) {
            $URLs[] = Colby::flexnameForCSSForClass(CBSitePreferences::siteURL(), $className);
        }

        return $URLs;
    }
}
