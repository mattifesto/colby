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
            (object)[
                'mainMenuItemName' => 'help',
                'menuItem' => (object)[
                    'className' => 'CBMenuItem',
                    'name' => 'Markaround',
                    'text' => 'Markaround',
                    'URL' => '/admin/?c=CBDocumentation&p=Markaround',
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

            case 'Markaround':
                return ['help', 'Markaround'];

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

            case 'Markaround':
                CBView::renderSpec((object)[
                    'className' => 'CBThemedTextView',
                    'contentAsMarkaround' => file_get_contents(__DIR__ . '/Markaround_documentation.markaround'),
                    'stylesTemplate' => <<<EOT

                        view {
                            background-color: var(--CBBackgroundColor);
                        }

EOT
                ]);
                break;

            default:

                $CSS = <<<EOT

                    main.CBDocumentation {
                        background-color: var(--CBBackgroundColor);
                    }

                    .CBDocumentation_container {
                        text-align: center;
                    }

                    .CBDocumentation_container a {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        height: 44px;
                    }
EOT;

                CBHTMLOutput::addCSS($CSS);

                CBHTMLOutput::pageInformation()->title = 'Documentation';

                CBView::renderSpec((object)[
                    'className' => 'CBPageTitleAndDescriptionView',
                ]);

                $menuModel = CBModelCache::fetchModelByID(CBHelpAdminMenu::ID());

                ?><div class="CBDocumentation_container"><?php

                $items = CBModel::valueToArray($menuModel, 'items');

                array_walk(
                    $items,
                    function ($item) {
                        $textAsHTML = cbhtml(CBModel::valueToString($item, 'text'));
                        $URLAsHTML = cbhtml(CBModel::valueToString($item, 'URL'));

                        ?>

                        <div>
                            <a href="<?= $URLAsHTML ?>"><?= $textAsHTML ?></a>
                        </div>

                        <?php
                    }
                );

                ?></div><?php

                break;
        }
    }
}
