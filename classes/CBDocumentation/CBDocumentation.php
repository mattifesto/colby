<?php

final class
CBDocumentation
{
    /* -- CBAdmin interfaces -- */



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



    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array
    {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.61.css',
                cbsysurl()
            ),
        ];
    }
    // CBHTMLOutput_CSSURLs()

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
        $classNames = CBLibrary::getAllClassDirectoryNames();

        ?>

        <div class="CBDocumentation_container">
            <div class="CBUI_title1">
                Classes
            </div>

            <?php

            sort($classNames);

            foreach ($classNames as $className) {
                $classDocumentationURL = (
                    '/admin/' .
                    '?c=CBAdmin_CBDocumentationForClass' .
                    '&className=' .
                    urlencode(
                        $className
                    )
                );

                ?>

                <div>
                    <a href="<?= $classDocumentationURL ?>">
                        <?= cbhtml($className) ?>
                    </a>
                </div>

                <?php
            }

            ?>

        </div>

        <?php
    }
    /* CBView_render() */

}
/* CBDocumentation_ClassListView */
