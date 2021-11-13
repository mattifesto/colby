<?php

/**
 * @deprecated 2018.02.26
 *
 *      This class no longer creates static width pages. This class will be
 *      removed as soon as removal is known to not cause issues.
 */
final class
CBPageSettingsForStaticPages {

    /**
     * @return [string]
     */
    static function CBPageSettings_htmlElementClassNames(): array {
        return ['CBLightTheme', 'CBStyleSheet'];
    }



    /**
     * @return [string]
     */
    static function CBPageSettings_requiredClassNames(): array {
        return [
            'CBEqualizePageSettingsPart',
            'CBResponsiveViewportPageSettingsPart',
            'CBGoogleTagManagerPageSettingsPart',
            'CBFacebookPageSettingsPart',
            'CB_CBPageSettingsPart_SitePreferences',
        ];
    }

}
