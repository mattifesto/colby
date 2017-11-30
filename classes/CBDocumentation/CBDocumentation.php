<?php

final class CBDocumentation {

    /**
     * @return [object]
     */
    static function CBAdmin_menuItems(): array {
        return [
            (object)[
                'mainMenuItemName' => 'help',
                'menuItem' => (object)[
                    'className' => 'CBMenuItem',
                    'name' => 'CBPageHelpers',
                    'text' => 'CBPageHelpers',
                    'URL' => '/admin/?c=CBDocumentation&p=CBPageHelpers',
                ],
            ],
        ];
    }

    /**
     * @param string $pageStub
     *
     * @return [string]
     */
    static function CBAdmin_menuNamePath(string $pageStub) {
        switch ($pageStub) {
            case 'CBPageHelpers':
                return ['help', 'CBPageHelpers'];

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
