<?php

/**
 * @deprecated 2021_06_30
 *
 *      This page has very few differences from CBPageSettingsForResponsivePages
 *      and none that justify another page settings class.
 */
final class
CBPageSettingsForAdminPages {

    /* -- CBPageSettings interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBPageSettings_htmlElementClassNames(
    ): array {
        return [
            'CBPageSettingsForAdminPages',
            'CBLightTheme',
            'CBStyleSheet',
        ];
    }
    /* CBPageSettings_htmlElementClassNames() */



    /**
     * @param Throwable $throwable
     *
     * @return void
     */
    static function
    CBPageSettings_renderErrorPage(
        Throwable $throwable
    ): void {
        $spec = (object)[
            'className' => 'CBViewPage',
            'classNameForSettings' => 'CBPageSettingsForAdminPages',
            'title' => 'Exception',
            'layout' => (object)[
                'className' => 'CBPageLayout',
                'customLayoutClassName' => 'CBAdminPageLayout',
            ],
            'sections' => [
                (object)[
                    'className' => 'CBExceptionView',
                ],
            ],
        ];

        CBExceptionView::pushThrowable($throwable);
        CBPage::renderSpec($spec);
        CBExceptionView::popThrowable();
    }
    /* CBPageSettings_renderErrorPage() */



    /**
     * @return [string]
     */
    static function
    CBPageSettings_requiredClassNames(
    ): array {
        return [
            'CBEqualizePageSettingsPart',
            'CBGoogleTagManagerPageSettingsPart',
            'CBResponsiveViewportPageSettingsPart',
        ];
    }
    /* CBPageSettings_requiredClassNames() */

}
