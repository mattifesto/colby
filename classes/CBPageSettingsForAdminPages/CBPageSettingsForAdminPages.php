<?php

final class CBPageSettingsForAdminPages {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_htmlClassNames(): array {
        return ['CBLightTheme', 'CBStyleSheet'];
    }

    /**
     * @param Throwable $throwable
     *
     * @return void
     */
    static function renderPageForException(Throwable $throwable): void {
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
     * @return void
     */
    static function CBHTMLOutput_renderHeadContent(): void {
        ?>

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <?php
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredHeadClassNames(): array {
        return ['CBEqualize'];
    }
}
