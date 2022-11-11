<?php

final class Documentation_CBContentStyleSheet {

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
            'help',
        ];
    }



    /**
     * @return void
     */
    static function CBAdmin_render(
    ): void {
        CBHTMLOutput::pageInformation()->title = (
            'CBContentStyleSheet Documentation'
        );

        $CSS = <<<EOT

            main.CBUIRoot
            {
                background-color:
                var(--CBBackgroundColor1);
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
                    __DIR__ .
                    '/Documentation_CBContentStyleSheet.cbmessage'
                ),
            ]
        );
    }
    /* CBAdmin_render() */



    /* -- CBInstall interfaces -- */



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
                'name' => 'CBContentStyleSheet',
                'text' => 'CBContentStyleSheet',
                'URL' => '/admin/?c=Documentation_CBContentStyleSheet',
            ]
        );

        CBModelUpdater::save($updater);
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBHelpAdminMenu'
        ];
    }

}
