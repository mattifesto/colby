<?php

final class Documentation_CBUI {

    /**
     * @return string
     */
    static function CBAdmin_group(): string {
        return 'Developers';
    }

    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return [
            'help',
        ];
    }

    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'CBUI Documentation';

        CBView::renderSpec((object)[
            'className' => 'CBPageTitleAndDescriptionView',
        ]);

        $message = file_get_contents(__DIR__ . '/Documentation_CBUI.mmk');

        ?>

        <div>
            <?= CBMessageMarkup::messageToHTML($message) ?>
        </div>

        <?php
    }

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $updater = CBModelUpdater::fetch((object)[
            'ID' => CBHelpAdminMenu::ID(),
        ]);

        $menuSpec = $updater->working;

        CBMenu::addOrReplaceItem(
            $menuSpec,
            (object)[
                'className' => 'CBMenuItem',
                'name' => 'CBUI',
                'text' => 'CBUI',
                'URL' => '/admin/?c=Documentation_CBUI',
            ]
        );

        CBModelUpdater::save($updater);
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBHelpAdminMenu'
        ];
    }
}
