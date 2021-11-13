<?php

final class
CBPageSettingsForResponsivePages {

    /* -- CBPageSettings interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBPageSettings_htmlElementClassNames(
    ): array {
        return [
            'CBPageSettingsForResponsivePages',
            'CBLightTheme',
            'CBStyleSheet',
        ];
    }
    /* CBPageSettings_htmlElementClassNames() */



    /**
     * @return [string]
     */
    static function
    CBPageSettings_requiredClassNames(
    ): array {
        return [
            'CBEqualizePageSettingsPart',
            'CBResponsiveViewportPageSettingsPart',
            'CBGoogleTagManagerPageSettingsPart',
            'CBFacebookPageSettingsPart',
            'CB_CBPageSettingsPart_SitePreferences',
        ];
    }
    /* CBPageSettings_requiredClassNames() */

}
