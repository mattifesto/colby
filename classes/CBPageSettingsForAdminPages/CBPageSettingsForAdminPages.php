<?php

final class CBPageSettingsForAdminPages {

    /**
     * @return [string]
     */
    static function CBPageSettings_htmlElementClassNames(): array {
        return [
            'CBPageSettingsForAdminPages',
            'CBLightTheme',
            'CBStyleSheet',
        ];
    }


    /**
     * @param Throwable $throwable
     *
     * @return void
     */
    static function CBPageSettings_renderErrorPage(Throwable $throwable): void {
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


    /**
     * @return [string]
     */
    static function CBPageSettings_requiredClassNames(): array {
        return [
            'CBEqualizePageSettingsPart',
            'CBResponsiveViewportPageSettingsPart',
        ];
    }
}
