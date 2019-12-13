<?php

final class CBDocumentation {

    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return string
     */
    static function CBAdmin_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return [
            'help'
        ];
    }



    /**
     * @return void
     */
    static function CBAdmin_render(): void {
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

        usort(
            $items,
            function ($a, $b) {
                $atext = CBModel::valueToString($a, 'text');
                $btext = CBModel::valueToString($b, 'text');

                return $atext <=> $btext;
            }
        );

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
    }
    /* CBAdmin_render() */

}
