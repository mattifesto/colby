<?php

final class Documentation_CBContentStyleSheet {

    /**
     * @return string
     */
    static function CBAdmin_group(): string {
        return 'Administrators';
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
        CBHTMLOutput::pageInformation()->title = 'CBContentStyleSheet Documentation';

        $CSS = <<<EOT

            main.CBUIRoot {
                background-color: var(--CBBackgroundColor);
            }

            /**
             * An inner div should be included when using this class to show the
             * verticaly margins of the content.
             */
            .Documentation_CBContentStyleSheet_example {
                background-color: hsl(0, 0%, 90%);
                border: 2px solid hsl(0, 0%, 85%);
                margin: 1em 0 5em;
            }

EOT;

        CBHTMLOutput::addCSS($CSS);

        CBView::renderSpec((object)[
            'className' => 'CBPageTitleAndDescriptionView',
        ]);

        CBView::renderSpec((object)[
            'className' => 'CBMessageView',
            'markup' => file_get_contents(__DIR__ . '/Documentation_CBContentStyleSheet.mmk'),
        ]);
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
                'name' => 'CBContentStyleSheet',
                'text' => 'CBContentStyleSheet',
                'URL' => '/admin/?c=Documentation_CBContentStyleSheet',
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
