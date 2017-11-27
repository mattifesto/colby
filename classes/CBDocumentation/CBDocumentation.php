<?php

final class CBDocumentation {

    /**
     * @param string $pageStub
     *
     * @return [string]
     */
    static function CBAdmin_menuNamePath(string $pageStub) {
        switch ($pageStub) {
            case 'CBPageHelpers':
                return ['help', 'api'];

            default:
                return ['help'];
        }
    }

    /**
     * @param string $pageStub
     *
     * @return void
     */
    static function CBAdmin_render(string $pageStub): void {
        switch ($pageStub) {
            case 'CBPageHelpers':
                CBView::renderspec((object)[
                    'className' => 'CBMessageView',
                    'CSSClassNames' => 'CBAPIStyleSheet',
                    'markup' => file_get_contents(__DIR__ . '/CBPageHelpers_documentation.mmk'),
                ]);
                break;

            default:
                break;
        }
    }
}
