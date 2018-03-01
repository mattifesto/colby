<?php

final class CBPageSettingsForResponsivePages {

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
        ];
    }
}
