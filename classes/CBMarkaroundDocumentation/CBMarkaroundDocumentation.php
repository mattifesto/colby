<?php

final class CBMarkaroundDocumentation {

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
        CBHTMLOutput::pageInformation()->title = 'Markaround Documentation';

        CBView::renderSpec((object)[
            'className' => 'CBPageTitleAndDescriptionView',
        ]);

        $CSS = <<<EOT

            main.CBUIRoot {
                background-color: var(--CBBackgroundColor);
            }

EOT;

        CBHTMLOutput::addCSS($CSS);

        CBView::renderSpec((object)[
            'className' => 'CBThemedTextView',
            'contentAsMarkaround' => file_get_contents(__DIR__ . '/Markaround_documentation.markaround'),
        ]);
    }

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $spec = CBModels::fetchSpecByID(CBHelpAdminMenu::ID());

        $spec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'Markaround',
            'text' => 'Markaround',
            'URL' => '/admin/?c=CBMarkaroundDocumentation',
        ];

        CBDB::transaction(function () use ($spec) {
            CBModels::save($spec);
        });
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBHelpAdminMenu'];
    }
}
