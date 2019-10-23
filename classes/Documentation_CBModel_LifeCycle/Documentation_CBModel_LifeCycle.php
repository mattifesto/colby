<?php

final class Documentation_CBModel_LifeCycle {

    /* -- CBAdmin interfaces -- -- -- -- -- */



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
        CBHTMLOutput::pageInformation()->title =
        'CBModel Life Cycle';

        $CSS = <<<EOT

            main.CBUIRoot {
                background-color: var(--CBBackgroundColor);
            }

EOT;

        CBHTMLOutput::addCSS($CSS);

        CBView::renderSpec(
            (object)[
                'className' => 'CBPageTitleAndDescriptionView',
            ]
        );

        CBView::renderSpec(
            (object)[
                'className' => 'CBMessageView',
                'markup' => file_get_contents(
                    __DIR__ . '/Documentation_CBModel_LifeCycle.mmk'
                ),
            ]
        );
    }



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $updater = CBModelUpdater::fetch(
            (object)[
                'ID' => CBHelpAdminMenu::ID(),
            ]
        );

        $menuSpec = $updater->working;

        CBMenu::addOrReplaceItem(
            $menuSpec,
            (object)[
                'className' => 'CBMenuItem',
                'name' => 'CBModel Life Cycle',
                'text' => 'CBModel Life Cycle',
                'URL' => '/admin/?c=Documentation_CBModel_LifeCycle',
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
