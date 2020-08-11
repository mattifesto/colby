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

        CBView::render(
            (object)[
                'className' => 'CBDocumentation_ClassListView',
            ]
        );
    }
    /* CBAdmin_render() */

}
/* CBDocumentation */



/**
 *
 */
final class CBDocumentation_ClassListView {

    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBContentStyleSheet',
        ];
    }



    /* -- CBView interfaces -- */



    /**
     * @param object $viewModel
     *
     * @return void
     */
    static function CBView_render(
        stdClass $viewModel
    ): void {
        $classNames = CBAdmin::fetchClassNames();

        ?>

        <div class="CBDocumentation_ClassListView CBUI_view_outer">
            <div class="CBUI_view_inner_text CBContentStyleSheet">
                <div class="CBUI_title1">
                    Classes
                </div>

                <dl>
                    <?php

                    sort($classNames);

                    foreach ($classNames as $className) {
                        $descriptionFilepath = Colby::findFile(
                            "classes/{$className}/{$className}_" .
                            'CBDocumentation_description.cbmessage'
                        );

                        ?>

                        <dt><?= cbhtml($className) ?>

                        <?php

                        if ($descriptionFilepath !== null) {
                            echo '<dd>';

                            echo CBMessageMarkup::messageToHTML(
                                file_get_contents($descriptionFilepath)
                            );
                        }
                    }

                    ?>
                </dl>
            </div>
        </div>

        <?php
    }
    /* CBView_render() */

}
/* CBDocumentation_ClassListView */
